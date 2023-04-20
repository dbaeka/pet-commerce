<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\File;
use Database\Factories\FileFactory;

class ProductSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $count = rand(100, 200);
        $categories = Category::limit($count)->get('uuid');
        $brands = Brand::limit($count)->get('uuid');
        /** @var File $image */
        $image = FileFactory::new()->create();
        $products = [];
        for ($i = 0; $i < $count; $i++) {
            $products[] = [
                'category_uuid' => $categories->random()->uuid,
                'uuid' => fake()->unique()->uuid(),
                'title' => fake()->sentence(),
                'price' => fake()->randomFloat(2, 10, 1000),
                'description' => fake()->paragraph(),
                'metadata' => json_encode([
                    'brand' => $brands->random()->uuid,
                    'image' => $image->uuid,
                ]),
            ];
        }
        $this->syncToDb($products);
    }
}
