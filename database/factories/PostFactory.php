<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
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
            'slug' => fn (array $attributes) => Str::slug($attributes['title']),
            'content' => fake()->paragraph(),
            'metadata' => [
                "author" => fake()->name(),
                "image" => fake()->uuid()
            ]
        ];
    }
}
