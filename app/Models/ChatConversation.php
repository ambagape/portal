<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChatConversation extends Model
{
    use Timestamp;

    public $fillable = ['coach_user_id', 'client_user_id'];

    public function clientUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_user_id');
    }

    public function coachUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function lastMessage(): HasOne
    {
        return $this->hasOne(ChatMessage::class)->latest();
    }

    public function unread(): int {

        return ChatMessage::query()
            ->where('chat_conversation_id', $this->id)
            ->where('seen', false)
            ->where('user_id', '!==', $this->id)
            ->count();
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ChatParticipant::class);
    }
}
