<?php

namespace App\Rebase;

use App\Models\User;

class SendMessage
{
    public function sendFCM($message, $title, $id, $unreadMessages, $conversation = null)
    {
        $url = env('FCM_SERVER_URL', '');

        $client = null;
        $coach = null;
        if ($conversation) {
            $client = User::findOrFail($conversation->client_user_id);
            $coach = User::findOrFail($conversation->coach_user_id);
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
        $fields = json_encode($fields);
        $headers = [
            'Authorization: key=' . env('FCM_SERVER_KEY', ''),
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        $result = curl_exec($ch);
        curl_close($ch);
    }
}
