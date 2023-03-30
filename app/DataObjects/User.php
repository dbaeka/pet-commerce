<?php

namespace App\DataObjects;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;

class User extends Data
{
    public int $id;
    public string $uuid;
    public bool $is_admin;
    public string $first_name;
    public string $last_name;
    public string $email;
    public ?string $avatar;
    public ?CarbonImmutable $email_verified_at;
    public string $address;
    public string $phone_number;
    public bool $is_marketing = false;
    public CarbonImmutable $updated_at;
    public CarbonImmutable $created_at;
    public ?CarbonImmutable $last_login_at;
}
