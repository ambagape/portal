<?php

namespace App\Events;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ChatMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $message;
    private $user;

    public function __construct(ChatMessage $message, User $user)
    {
        $this->message = $message;
        $this->user = $user;
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
