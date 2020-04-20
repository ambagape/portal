<?php

namespace App\Events;

use App\Models\ChatMessage;
use App\Models\User;
use App\Rebase\SendMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $message;
    private $user;

    public function __construct(ChatMessage $message, User $user, $sender, int $unreadMessages, $conversation = null)
    {
        $this->message = $message;
        $this->user = $user;

        // Push notification
        $user->tokens->map(function ($token) use ($sender, $message, $unreadMessages, $conversation) {
            (new SendMessage)->SendFCM(
                $message->message,
                $sender->full_name,
                $token->push_token,
                $unreadMessages,
                $conversation
            );
        });
    }

    public function broadcastWith()
    {
        return (new \App\Http\Resources\ChatMessage($this->message))->response()->getData(true);
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->user->rebase_user_id);
    }

}
