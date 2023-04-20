<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'is_admin' => false,
            'uuid' => fake()->unique()->uuid(),
            'email_verified_at' => now()->toDateTimeString(),
            'password' => bcrypt('password'),
            'address' => fake()->streetAddress(),
            'phone_number' => fake()->e164PhoneNumber()
        ];
    }

    public function admin(): self
    {
        return $this->state(fn () => ['is_admin' => true]);
    }

    public function regular(): self
    {
        return $this->state(fn () => ['is_admin' => false]);
    }
}
