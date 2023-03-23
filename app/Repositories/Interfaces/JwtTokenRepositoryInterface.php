<?php

namespace App\Repositories\Interfaces;

use App\Dtos\Token;
use App\Dtos\User;

interface JwtTokenRepositoryInterface
{
    public function createToken(Token $token): bool;

    public function getTokenByUniqueId(string $unique_id): ?Token;

    public function updateTokenLastUsed(): bool;

    public function expireToken(string $unique_id): bool;

    public function getUserByUniqueId(mixed $unique_id): ?User;
}
