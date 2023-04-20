<?php

namespace Database\Seeders;

use Database\Factories\BrandFactory;

class BrandSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = BrandFactory::new()->count(5)->make();
        $this->syncToDb($brands->toArray());
    }
}
