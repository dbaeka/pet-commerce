<?php

namespace App\DataObjects;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;

class Token extends Data
{
    public string $user_uuid;
    public string $unique_id;
    public string $token_title;
    /** @var array<string> $restrictions */
    public array $restrictions = [];
    /** @var array<string> $permissions */
    public array $permissions = [];
    public ?CarbonImmutable $expires_at;
}
