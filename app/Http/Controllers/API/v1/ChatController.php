<?php

namespace App\Http\Controllers\API\v1;

use App\Events\ChatMessageSent;
use App\Http\Controllers\Controller;
use App\Http\Resources\ChatConversation as ChatConversationResource;
use App\Http\Resources\ChatMessage as ChatMessageResource;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Token;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use DB;

class ChatController extends Controller
{

    public function conversations(Request $request): AnonymousResourceCollection
    {
        $token = $this->getToken($request);

        $conversations = ChatConversation::query()
            ->select('chat_conversations.*', DB::raw('MAX(chat_messages.created_at)'))
            ->leftJoin('chat_messages', 'chat_messages.chat_conversation_id', '=', 'chat_conversations.id')
            ->with(['lastMessage'])
            ->where(static function (Builder $query) use ($token) {
                return $query
                    ->where('client_user_id', $token->user_id)
                    ->orWhere('coach_user_id', $token->user_id);
            })
            ->orderBy(DB::raw('MAX(chat_messages.created_at)'), 'DESC')
            ->groupBy('chat_conversations.id')
            ->get();

        return ChatConversationResource::collection($conversations);
    }

    public function conversation(Request $request)
    {
        $token = $this->getToken($request);

        $validated = $request->validate([
            'coach_rebase_id' => 'required',
            'client_rebase_id' => 'required'
        ]);

        $coachUser = User::query()->where('rebase_user_id', $validated['coach_rebase_id'])->firstOrFail();
        $clientUser = User::query()->where('rebase_user_id', $validated['client_rebase_id'])->firstOrFail();

        $conversation = ChatConversation::query()
            ->where('coach_user_id', $coachUser->id)
            ->where('client_user_id', $clientUser->id)
            ->first();

        // Create conversation with participants if it didn't exist
        if (!$conversation) {
            $conversation = ChatConversation::create([
                'coach_user_id' => $coachUser->id,
                'client_user_id' => $clientUser->id
            ]);
        }

        return new ChatConversationResource($conversation);
    }

    public function messages(Request $request, ChatConversation $chat_conversation): AnonymousResourceCollection
    {
        $token = $this->getToken($request);
        $this->isParticipant($token->user, $chat_conversation);

        // Mark all messages as read
        ChatMessage::query()
            ->where('chat_conversation_id', $chat_conversation->id)
            ->where('user_id', '!=', auth()->user()->id)
            ->update([
                'seen' => true
            ]);

        // Load all messages, or greater then given message id
        $chat_conversation->load([
            'messages' => function ($query) use ($request) {
                if ($request->get('filter')) {
                    $query->where('id', '>', $request->get('filter'));
                }
                return $query;
            }
        ]);

        return ChatMessageResource::collection($chat_conversation->messages);
    }

    public function sendMessage(Request $request, ChatConversation $chat_conversation)
    {
        $token = $this->getToken($request);
        $this->isParticipant($token->user, $chat_conversation);

        $validated = $request->validate([
            'message' => 'required'
        ]);

        $message = ChatMessage::create([
            'message' => $validated['message'],
            'chat_conversation_id' => $chat_conversation->id,
            'user_id' => $token->user_id,
            'seen' => false
        ]);


        if ($chat_conversation->client_user_id === $token->user_id) {
            $token = $chat_conversation->coachUser->tokens[0];
            $unreadMessages = $this->unreadMessages($token);
            event(new ChatMessageSent($message, $chat_conversation->coachUser, auth()->user(), $unreadMessages, $chat_conversation));
        }

        if ($chat_conversation->coach_user_id === $token->user_id) {
            $token = $chat_conversation->clientUser->tokens[0];
            $unreadMessages = $this->unreadMessages($token);
            event(new ChatMessageSent($message, $chat_conversation->clientUser, auth()->user(), $unreadMessages, $chat_conversation));
        }

        return new ChatMessageResource($message->load('user'));
    }

    public function unread(Request $request) {
        $token = $this->getToken($request);

        $count = $this->unreadMessages($token);

        return response()->json(['data' => [
            'count' => $count
        ]], 200);
    }

    private function isParticipant(User $user, ChatConversation $chat_conversation)
    {
        $isParticipant = $chat_conversation->client_user_id === $user->id || $chat_conversation->coach_user_id === $user->id;

        if (!$isParticipant) {
            abort(403);
        }
    }

    private function getToken(Request $request)
    {
        $token = explode(" ", $request->header('Authorization'))[1];

        return Token::where('token', $token)->first();
    }

    private function unreadMessages(Token $token): int
    {
        $conversationIds = ChatConversation::query()
            ->with(['lastMessage'])
            ->where(static function (Builder $query) use ($token) {
                return $query
                    ->where('client_user_id', $token->user_id)
                    ->orWhere('coach_user_id', $token->user_id);
            })
            ->pluck('id');


        return ChatMessage::query()
            ->where('seen', false)
            ->whereIn('chat_conversation_id', $conversationIds)
            ->where('user_id' , '!=', $token->user_id)
            ->count();
    }
}
