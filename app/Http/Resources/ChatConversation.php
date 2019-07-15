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
            'participant' => new ChatParticipant(
                $this->whenLoaded('participants')->filter(function ($participant) {
                    return $participant->user_id !== auth()->user()->id;
                })->first()
            )
        ];
    }
}
