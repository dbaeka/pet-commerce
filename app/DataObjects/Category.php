<?php

namespace App\DataObjects;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class Category extends Data
{
    public string $title;
    public string|Optional $uuid;
    public string|Optional $slug;
    public CarbonImmutable|Optional $updated_at;
    public CarbonImmutable|Optional $created_at;
}
