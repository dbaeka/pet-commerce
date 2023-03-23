<?php

namespace App\Repositories;

use App\Dtos\Token;
use App\Dtos\User;
use App\Repositories\Interfaces\JwtTokenRepositoryInterface;

class JwtTokenRepository implements JwtTokenRepositoryInterface
{
    public function createToken(Token $token): bool
    {
        return false;
    }

    public function getTokenByUniqueId(string $unique_id): ?Token
    {
        return null;
    }

    public function updateTokenLastUsed(): bool
    {
        return false;
    }

    public function expireToken(string $unique_id): bool
    {
        return false;
    }

    public function getUserByUniqueId(mixed $unique_id): ?User
    {
        return null;
    }

    public function checkTokenExists(string $unique_id): bool
    {
        return false;
    }
}
