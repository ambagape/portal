<?php

namespace App\Http\Controllers\API\v1;

use App\Events\ChatMessageSent;
use App\Http\Controllers\Controller;
use App\Http\Resources\ChatConversation as ChatConversationResource;
use App\Http\Resources\ChatMessage as ChatMessageResource;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\ChatParticipant;
use App\Models\Token;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{

    public function conversations(Request $request)
    {
        $token = $this->getToken($request);

        $conversations = ChatConversation::query()
            ->with(['lastMessage'])
            ->whereHas('participants', function ($query) use ($token) {
                $query->where('user_id', '=', $token->user_id);
            })
            ->get();

        return ChatConversationResource::collection($conversations);
    }

    public function startConversation(Request $request)
    {
        $token = $this->getToken($request);

        $validated = $request->validate([
            'rebase_user_id' => 'required'
        ]);

        // Get all participants
        $participants = User::query()
            ->orWhere('id', $token->user_id)
            ->orWhere('rebase_user_id', $validated['rebase_user_id'])
            ->get();

        if ($participants->count() < 2) {
            abort(404);
        }

        // Get conversation with these participants, if it exists
        $conversation = ChatConversation::whereHas('participants', function ($query) use ($token, $validated) {
            $query
                ->where('user_id', '=', $token->user_id)
                ->orWhere('user_id', '=', $validated['rebase_user_id']);
        })->first();

        // Create conversation with participants if it didn't exist
        if (!$conversation) {
            $conversation = ChatConversation::create();

            foreach ($participants as $participant) {
                ChatParticipant::create([
                    'user_id' => $participant->id,
                    'chat_conversation_id' => $conversation->id
                ]);
            }

            $conversation = $conversation->fresh(['participants']);
        }

        return new ChatConversationResource($conversation);
    }

    public function messages(Request $request, ChatConversation $chat_conversation)
    {
        $token = $this->getToken($request);
        $this->isParticipant($token->user, $chat_conversation);

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
            'user_id' => $token->user_id
        ]);

        $chat_conversation->participants->each(function ($participant) use ($message) {
            if ($participant->user->rebase_user_id !== auth()->user()->rebase_user_id) {
                event(new ChatMessageSent($message, $participant->user));
            }
        });

        return new ChatMessageResource($message);
    }

    private function isParticipant(User $user, ChatConversation $chat_conversation)
    {
        $isParticipant = $chat_conversation->participants->filter(
                function ($participant) use ($user) {
                    return $participant->user_id === $user->id;
                })->count() > 0;

        if (!$isParticipant) {
            abort(403);
        }
    }

    private function getToken(Request $request)
    {
        $token = explode(" ", $request->header('Authorization'))[1];

        return Token::where('token', $token)->first();
    }
}
