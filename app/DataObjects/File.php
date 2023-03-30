<?php

namespace App\DataObjects;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;

class File extends Data
{
    public string $path;
    public string $uuid;
    public string $name;
    public string $size;
    public string $type;
    public CarbonImmutable $updated_at;
    public CarbonImmutable $created_at;
}
