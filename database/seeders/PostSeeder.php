<?php

namespace Database\Seeders;

use App\Models\File;
use Database\Factories\PostFactory;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PostFactory::new()->count(10)->sequence(
            fn () => ['metadata' => [
                "image" => File::all()->random()->uuid,
                "author" => fake()->name()
            ]]
        )->create();
    }
}
