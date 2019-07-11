<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ChatMessagesSeen implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $messages;
    private $user;

    public function __construct($messages, User $user)
    {
        $this->messages = $messages;
        $this->user = $user;
    }

    public function broadcastWith()
    {
        return \App\Http\Resources\ChatMessage::collection($this->messages)->response()->getData(true);
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->user->rebase_user_id);
    }
}
