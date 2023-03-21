<?php

namespace Database\Seeders;

use App\Models\File;
use App\Models\Promotion;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Promotion::factory()->count(10)->sequence(
            fn () => ['metadata' => [
                "image" => File::all()->random()->uuid,
                "valid_from" => fake()->date('Y-m-d', '-2 days'),
                "valid_to" => now()->addDays(rand(1, 100))->format('Y-m-d'),
            ]]
        )->create();
    }
}
