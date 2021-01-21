<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = [
        'user_id', 'chat_conversation_id', 'message',
    ];

    protected $with = [
        'user',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function conversation()
    {
        return $this->belongsTo(ChatConversation::class);
    }

    public function participant()
    {
        return $this->belongsTo(ChatParticipant::class, 'user_id', 'user_id');
    }
}
