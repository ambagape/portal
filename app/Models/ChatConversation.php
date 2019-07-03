<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatConversation extends Model
{

    protected $with = [
        'participants'
    ];

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function lastMessage()
    {
        return $this->hasOne(ChatMessage::class)->latest();
    }

    public function participants()
    {
        return $this->hasMany(ChatParticipant::class);
    }
}
