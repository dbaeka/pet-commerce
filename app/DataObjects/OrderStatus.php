<?php

namespace App\DataObjects;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;

class OrderStatus extends Data
{
    public string $title;
    public string $uuid;
    public CarbonImmutable $updated_at;
    public CarbonImmutable $created_at;
}
