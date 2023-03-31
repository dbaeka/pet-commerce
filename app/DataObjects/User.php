<?php

namespace App\DataObjects;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class User extends Data
{
    public int|Optional $id;
    public string|Optional $uuid;
    public bool|Optional $is_admin;
    public string|Optional $first_name;
    public string|Optional $last_name;
    public string|Optional $email;
    public string|Optional $password;
    public string|Optional|null $avatar;
    public CarbonImmutable|Optional|null $email_verified_at;
    public string|Optional $address;
    public string|Optional $phone_number;
    public bool|Optional|null $is_marketing = false;
    public CarbonImmutable|Optional $updated_at;
    public CarbonImmutable|Optional $created_at;
    public CarbonImmutable|Optional|null $last_login_at;
}
