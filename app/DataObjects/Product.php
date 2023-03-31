<?php

namespace App\DataObjects;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class Product extends Data
{
    public string $title;
    public string|Optional $uuid;
    public string|Optional $category_uuid;
    public float $price;
    public Brand|Optional|null $brand;
    public CarbonImmutable|Optional $updated_at;
    public CarbonImmutable|Optional $created_at;
    public string|Optional $description;
    public ProductMetadata|Optional $metadata;
}
