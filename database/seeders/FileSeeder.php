<?php

namespace Database\Seeders;

use Database\Factories\FileFactory;

class FileSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // File Seeder specifically for images for promotions and posts
        $files = FileFactory::new()->count(6)->make();
        $this->syncToDb($files->toArray());
    }
}
