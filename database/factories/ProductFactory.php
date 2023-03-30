<?php

namespace Database\Factories;

use App\DataObjects\ProductMetadata;
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
        /** @var Category $category */
        $category = CategoryFactory::new()->create();
        /** @var Brand $brand */
        $brand = BrandFactory::new()->create();
        /** @var File $image */
        $image = FileFactory::new()->create();

        return [
            'category_uuid' => $category->uuid,
            'uuid' => fake()->unique()->uuid(),
            'title' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 10, 1000),
            'description' => fake()->paragraph(),
            'metadata' => ProductMetadata::from([
                'brand' => $brand->uuid,
                'image' => $image->uuid,
            ]),
        ];
    }
}
