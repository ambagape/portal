<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        'rebase_user_id',
        'full_name',
    ];

    public function tokens()
    {
        return $this->hasMany(Token::class);
    }
}
