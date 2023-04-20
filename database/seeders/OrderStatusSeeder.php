<?php

namespace Database\Seeders;

use Database\Factories\OrderStatusFactory;

class OrderStatusSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statues = OrderStatusFactory::new()->count(5)->sequence(
            ...array_map(
                fn ($value) => ['title' => $value],
                ['open', 'pending payment', 'paid', 'shipped', 'cancelled']
            )
        )->make();
        $this->syncToDb($statues->toArray());
    }
}
