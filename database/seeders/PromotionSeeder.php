<?php

namespace Database\Seeders;

use App\Models\File;
use Database\Factories\PromotionFactory;

class PromotionSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $promotions = [];
        for ($i = 0; $i < 10; $i++) {
            $promotion = PromotionFactory::new()->definition();
            $promotion['metadata'] = json_encode([
                "image" => File::all()->random()->uuid,
                "valid_from" => fake()->date('Y-m-d', '-2 days'),
                "valid_to" => now()->addDays(rand(1, 100))->format('Y-m-d'),
            ]);
            $promotions[] = $promotion;
        }
        $this->syncToDb($promotions);
    }
}
