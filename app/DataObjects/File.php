<?php

namespace App\DataObjects;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class File extends Data
{
    public string $path;
    public string|Optional $uuid;
    public string $name;
    public string $size;
    public string $type;
    public CarbonImmutable|Optional $updated_at;
    public CarbonImmutable|Optional $created_at;
}
