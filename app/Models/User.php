<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'rebase_user_id',
        'full_name',
    ];

    public function tokens(): HasMany
    {
        return $this->hasMany(Token::class);
    }
}
