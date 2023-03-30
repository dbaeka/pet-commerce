<?php

namespace App\DataObjects;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;

class Promotion extends Data
{
    public string $title;
    public string $uuid;
    public string $content;
    public PromotionMetadata $metadata;
    public CarbonImmutable $updated_at;
    public CarbonImmutable $created_at;
}
