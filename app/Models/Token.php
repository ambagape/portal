<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $fillable = [
        'token',
        'user_id',
        'push_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
