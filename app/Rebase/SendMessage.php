<?php

namespace App\Rebase;


class SendMessage
{
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
