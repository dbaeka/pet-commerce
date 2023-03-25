<?php

namespace Database\Seeders;

use Database\Factories\FileFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Str;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // File Seeder specifically for images for promotions and posts
        $full_path = fake()->image(dir: storage_path('app/pet_store_files'), category: 'animals');
        $relative_path = Str::after($full_path, storage_path());
        FileFactory::new()->count(6)->create([
            'name' => fn () => fake()->unique()->sentence(),
            'path' => $relative_path,
            'size' => FileFacade::size($full_path),
            'type' => FileFacade::mimeType($full_path)
        ]);
    }
}
