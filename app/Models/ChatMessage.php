<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $fillable = [
        'user_id', 'chat_conversation_id', 'message',
    ];

    protected $with = [
        'user',
    ];

    protected $casts = [
        'message' => 'encrypted'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatConversation::class);
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(ChatParticipant::class, 'user_id', 'user_id');
    }
}
