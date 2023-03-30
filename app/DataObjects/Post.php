<?php

namespace App\DataObjects;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;

class Post extends Data
{
    public string $title;
    public string $uuid;
    public string $slug;
    public string $content;
    public PostMetadata $metadata;
    public CarbonImmutable $updated_at;
    public CarbonImmutable $created_at;
}
