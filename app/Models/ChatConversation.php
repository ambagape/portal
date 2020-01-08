<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Model;

class ChatConversation extends Model
{
    use Timestamp;

    public $fillable = ['coach_user_id', 'client_user_id'];

    public function clientUser()
    {
        return $this->belongsTo(User::class, 'client_user_id');
    }

    public function coachUser()
    {
        return $this->belongsTo(User::class, 'coach_user_id');
    }

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
