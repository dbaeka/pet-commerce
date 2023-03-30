<?php

namespace App\DataObjects;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;

class Category extends Data
{
    public string $title;
    public string $uuid;
    public string $slug;
    public CarbonImmutable $updated_at;
    public CarbonImmutable $created_at;
}
