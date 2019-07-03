<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChatMessage extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'send_at' => $this->created_at->format('Y-m-d H:i:s'),
            'seen' => $this->seen,
            'conversation_id' => $this->chat_conversation_id,
            'user_id' => $this->user_id,
            'user' => $this->whenLoaded('user'),
        ];
    }
}
