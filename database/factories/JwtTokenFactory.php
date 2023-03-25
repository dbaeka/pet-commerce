<?php

namespace Database\Factories;

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
        return [
            'user_id' => UserFactory::new(),
            'unique_id' => fake()->unique()->uuid(),
            'token_title' => fake()->text(),
            'expires_at' => fake()->dateTime('+ 10 hours')
        ];
    }
}
