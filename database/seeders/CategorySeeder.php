<?php

namespace Database\Seeders;

use Database\Factories\CategoryFactory;

class CategorySeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = CategoryFactory::new()->count(20)->make();
        $this->syncToDb($categories->toArray());
    }
}
