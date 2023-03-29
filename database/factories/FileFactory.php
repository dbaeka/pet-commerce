<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\File>
 */
class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fn () => fake()->unique()->sentence(),
            'path' => fake()->filePath(),
            'size' => fake()->randomNumber(4),
            'type' => fake()->mimeType(),
            'uuid' => fake()->unique()->uuid(),
        ];
    }

    public function local(string $file_path): self
    {
        $storage_path = config('app.pet_shop_file_dir');
        $file_name = basename($file_path);
        $full_path = storage_path($storage_path) . $file_name;
        if (!FileFacade::exists($full_path)) {
            FileFacade::copy($file_path, $full_path);
        }
        $relative_path = Str::after($full_path, storage_path());
        return $this->state(fn () => [
            'path' => $relative_path,
            'size' => FileFacade::size($full_path),
            'type' => FileFacade::mimeType($full_path),
        ]);
    }

    public function online(): self
    {
        $storage_path = config('app.pet_shop_file_dir');
        $full_path = fake()->image(dir: storage_path($storage_path), category: 'animals');
        $relative_path = Str::after($full_path, storage_path());
        return $this->state(fn () => [
            'path' => $relative_path,
            'size' => FileFacade::size($full_path),
            'type' => FileFacade::mimeType($full_path),
        ]);
    }
}
