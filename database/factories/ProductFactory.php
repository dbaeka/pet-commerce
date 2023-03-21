<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_uuid' => Category::factory()->create()->uuid,
            'uuid' => fake()->unique()->uuid(),
            'title' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 10, 1000),
            'description' => fake()->paragraph(),
            'metadata' => [
                'brand' => Brand::factory()->create()->uuid,
                'image' => File::factory()->create()->uuid,
            ],
        ];
    }
}
