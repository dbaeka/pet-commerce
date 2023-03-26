<?php

namespace App\Dtos;

class Token extends BaseDto
{
    protected ?int $id = null;
    public ?int $user_id = null;
    public string $unique_id = '';
    public string $token_title = '';
    /** @var array<string> $restrictions */
    public array $restrictions = [];
    /** @var array<string> $permissions */
    public array $permissions = [];
    public ?string $expires_at = null;
    private string $token_value = '';

    public function getTokenValue(): string
    {
        return $this->token_value;
    }

    public function setTokenValue(string $value): void
    {
        $this->token_value = $value;
    }

    public function withToken(string $value): Token
    {
        $this->setTokenValue($value);
        return $this;
    }
}
