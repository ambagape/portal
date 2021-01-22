<?php

namespace App\Rebase;

use App\Models\ChatConversation;
use Illuminate\Support\Facades\Http;

class SendMessage
{
    public function sendFCM(string $message, string $title, string $id, int $unreadMessages, ChatConversation $conversation = null)
    {
        $url = env('FCM_SERVER_URL', '');

        $client = null;
        $coach = null;
        if ($conversation) {
            $client = $conversation->clientUser;
            $coach = $conversation->coachUser;
        }

        $fields = [
            'to' => $id,
            'notification' => [
                'body' => $message,
                'title' => $title,
                'sound' => 'default',
                'badge' => $unreadMessages,
            ],
            'data' => [
                'conversation_id' => optional($conversation)->id,
                'conversation_client_user_id' => optional($client)->rebase_user_id,
                'conversation_coach_user_id' => optional($coach)->rebase_user_id,
                'conversation_client_full_name' => optional($client)->full_name,
                'conversation_coach_full_name' => optional($coach)->full_name,
            ],
        ];

        $response = Http::withHeaders([
            'Authorization' => 'key=' . env('FCM_SERVER_KEY', ''),
        ])
        ->post($url, $fields);

        $response->throw();
    }
}
