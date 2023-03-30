<?php

namespace App\DataObjects;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;

class Product extends Data
{
    public string $title;
    public string $uuid;
    public string $category_uuid;
    public float $price;
    public CarbonImmutable $updated_at;
    public CarbonImmutable $created_at;
    public string $description;
    public ProductMetadata $metadata;
}
