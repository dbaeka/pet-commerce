<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            FileSeeder::class,
            BrandSeeder::class,
            CategorySeeder::class,
            OrderStatusSeeder::class,
            PostSeeder::class,
            PromotionSeeder::class,
            PaymentSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class
        ]);
    }
}
