<?php

namespace App\DataObjects;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;

class PromotionMetadata extends Data
{
    public CarbonImmutable $valid_to;
    public CarbonImmutable $valid_from;
    public string $image;
}
