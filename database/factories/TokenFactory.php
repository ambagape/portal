<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TokenFactory extends Factory
{
    protected $model = \App\Models\Token::class;

    public function definition()
    {
        return [
            'token' => Str::random(10),
            'user_id' => User::all()->random(1)[0]->id,
            'push_token' => Str::random(10),
        ];
    }
}
