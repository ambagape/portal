<?php

namespace App\Http\Resources;

use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatConversation extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $token = $this->getToken($request);
        return [
            'id' => $this->id,
            'last_message' => new ChatMessage($this->whenLoaded('lastMessage')),
            'client' => $this->clientUser,
            'coach' => $this->coachUser,
            'unread_messages' => $this->unread($token->user_id)
        ];
    }

    private function getToken(Request $request)
    {
        $token = explode(" ", $request->header('Authorization'))[1];

        return Token::where('token', $token)->first();
    }

}
