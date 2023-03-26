<?php

namespace App\Dtos;

class User extends BaseDto
{
    protected ?int $id = null;
    public string $uuid = '';
    protected bool $is_admin = false;
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public ?string $avatar = '';
    public ?string $email_verified_at = '';
    public string $address = '';
    public string $phone_number = '';
    public bool $is_marketing = false;
    public string $updated_at = '';
    public string $created_at = '';
    public ?string $last_login_at = '';

    public function getId(): int
    {
        return $this->id ?: 0;
    }

    public function getIsAdmin(): bool
    {
        return $this->is_admin;
    }
}
