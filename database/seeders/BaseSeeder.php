<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

abstract class BaseSeeder extends Seeder
{
    protected string $table;

    /**
     * @param array<int|string, mixed> $data
     * @return void
     */
    final protected function syncToDb(array $data): void
    {
        $table = $this->table ?? $this->guessTableName();
        DB::table($table)->insert($data);
    }

    private function guessTableName(): string
    {
        $base_name = class_basename($this);
        $base_name = str_replace(['Seeder', 'seeder'], '', $base_name);
        return strtolower(Str::snake(Str::pluralStudly($base_name)));
    }
}
