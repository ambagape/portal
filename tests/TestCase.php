<?php

namespace Tests;

use App\Models\Token;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Str;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function apiLogin(): Token
    {

        $user = User::factory()
            ->create();

        // Delete other tokens
        Token::where('user_id', $user->id)->delete();

        // Create new token
        return Token::create([
            'user_id' => $user->id,
            'token' => Str::random(60) . time(),
        ]);

    }
}
