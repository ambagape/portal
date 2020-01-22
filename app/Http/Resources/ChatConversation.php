<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChatConversation extends JsonResource
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
            'last_message' => new ChatMessage($this->whenLoaded('lastMessage')),
            'client' => $this->clientUser,
            'coach' => $this->coachUser,
            'unread_messages' => $this->unread()
        ];
    }
}
