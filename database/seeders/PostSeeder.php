<?php

namespace Database\Seeders;

use App\Models\File;
use Database\Factories\PostFactory;
use Illuminate\Support\Str;

class PostSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = [];
        for ($i = 0; $i < 10; $i++) {
            $post = PostFactory::new()->definition();
            $post['metadata'] = json_encode([
                "image" => File::all()->random()->uuid,
                "author" => fake()->name()
            ]);
            $post['slug'] = Str::slug($post['title']);
            $posts[] = $post;
        }
        $this->syncToDb($posts);
    }
}
