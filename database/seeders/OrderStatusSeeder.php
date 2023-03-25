<?php

namespace Database\Seeders;

use Database\Factories\OrderStatusFactory;
use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OrderStatusFactory::new()->count(5)->sequence(
            ...array_map(
                fn ($value) => ['title' => $value],
                ['open', 'pending payment', 'paid', 'shipped', 'cancelled']
            )
        )->create();
    }
}
