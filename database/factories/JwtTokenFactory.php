<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JwtToken>
 */
class JwtTokenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /** @var User $user */
        $user = UserFactory::new()->create();
        return [
            'user_uuid' => $user->uuid,
            'unique_id' => fake()->unique()->uuid(),
            'token_title' => fake()->text(),
            'expires_at' => fake()->dateTime('+ 10 hours')
        ];
    }
}
