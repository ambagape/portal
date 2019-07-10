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

    public function __construct(ChatMessage $message, User $user, $sender)
    {
        $this->message = $message;
        $this->user = $user;

        // Push notification
        $user->tokens->map(function ($token) use ($sender, $message) {
            $this->sendFCM(
                $message->message,
                $sender->full_name,
                $token->push_token);
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

    public function sendFCM($message, $title, $id) {
        $url = env('FCM_SERVER_URL', '');
        $fields = array (
            'to' => $id,
            'notification' => array (
                "body" => $message,
                "title" => $title,
                "sound" => "default"
            )
        );
        $fields = json_encode($fields);
        $headers = array (
            'Authorization: key=' . env('FCM_SERVER_KEY', ''),
            'Content-Type: application/json'
        );

        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, true );
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );

        $result = curl_exec ( $ch );
        curl_close ( $ch );
    }
}
