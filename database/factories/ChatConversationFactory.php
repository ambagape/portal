<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatConversationFactory extends Factory
{
    protected $model = \App\Models\ChatConversation::class;

    public function definition()
    {
        return [
            'coach_user_id' => User::all()->random(1)[0]->id,
            'client_user_id' => User::all()->random(1)[0]->id,
        ];
    }
}
