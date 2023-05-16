<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'full_name' => $this->faker->name,
            'rebase_user_id' => $this->faker->unique()->safeEmail,
        ];
    }
}
