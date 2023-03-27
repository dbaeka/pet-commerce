<?php

namespace App\Dtos;

use App\Values\ProductMetadata;

class Product extends BaseDto
{
    protected array $casts = [
        'metadata' => \App\Casts\ProductMetadata::class
    ];

    public string $title = '';
    public string $uuid = '';
    public string $category_uuid = '';
    public ?float $price = null;
    public string $updated_at = '';
    public string $created_at = '';
    public string $description = '';
    public ?ProductMetadata $metadata = null;
}
