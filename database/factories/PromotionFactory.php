<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Promotion>
 */
class PromotionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->unique()->uuid(),
            'title' => fake()->sentence(),
            'content' => fake()->paragraph(),
            'metadata' => [
                "valid_from" => fake()->date('Y-m-d', '-2 days'),
                "valid_to" => now()->addDays(rand(1, 100))->format('Y-m-d'),
                "image" => fake()->uuid()
            ]
        ];
    }

    public function invalid(): self
    {
        return $this->state(fn () => [
            'metadata' => [
                "valid_from" => fake()->date('Y-m-d', '-2 days'),
                "valid_to" => now()->subDay()->format('Y-m-d'),
                "image" => fake()->uuid()
            ]
        ]);
    }

    public function valid(): self
    {
        return $this->state(fn () => [
            'metadata' => [
                "valid_from" => fake()->date('Y-m-d', '-2 days'),
                "valid_to" => now()->addDays(rand(1, 100))->format('Y-m-d'),
                "image" => fake()->uuid()
            ]
        ]);
    }
}
