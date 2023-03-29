<?php

namespace Database\Seeders;

use Database\Factories\FileFactory;
use Illuminate\Database\Seeder;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // File Seeder specifically for images for promotions and posts
        FileFactory::new()->count(6)->create();
    }
}
