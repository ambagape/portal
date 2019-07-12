<?php

namespace App\Http\Controllers\API\v1;

use App\Events\ChatMessageSent;
use App\Events\ChatMessagesSeen;
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
            ->with(['lastMessage',
                'participants' => function ($query) use ($token) {
                    return $query->where('user_id', '!=', $token->user_id);
                }
            ])
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

        $chat_conversation->participants->each(function ($participant) use ($message) {
            if ($participant->user->rebase_user_id !== auth()->user()->rebase_user_id) {
                event(new ChatMessageSent($message, $participant->user, auth()->user()));
            }
        });

        return new ChatMessageResource($message->load('user'));
    }

    public function markAsRead(Request $request) {
        $validated = $request->validate([
            'conversation_id' => 'required'
        ]);

        $token = $this->getToken($request);
        $chat_conversation = ChatConversation::where('id', $validated['conversation_id'])->firstOrFail();
        $this->isParticipant($token->user, $chat_conversation);

        $chat_conversation->participants->each(function ($participant) use ($chat_conversation) {
            if ($participant->user->rebase_user_id !== auth()->user()->rebase_user_id) {
                $messages = ChatMessage::query()
                    ->where('chat_conversation_id', $chat_conversation->id)
                    ->where('user_id', $participant->user->id)
                    ->where('seen', false)
                    ->get();

                event(new ChatMessagesSeen($messages, $participant->user));
            }
        });

        ChatMessage::query()
            ->where('chat_conversation_id', $chat_conversation->id)
            ->where('user_id', '!=', auth()->user()->id)
            ->where('seen', false)
            ->update([
                'seen' => true
            ]);

        return response()->json(['success' => 'success'], 200);
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
