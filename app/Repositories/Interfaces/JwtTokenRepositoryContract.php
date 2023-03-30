<?php

namespace App\Repositories\Interfaces;

use App\Dtos\Token;
use App\Dtos\User;

interface JwtTokenRepositoryContract
{
    public function createToken(Token $token): ?int;

    public function checkTokenExists(string $unique_id): bool;

    public function updateTokenLastUsed(string $unique_id): bool;

    public function deleteToken(string $unique_id): bool;

    public function getUserByUniqueId(string $unique_id): ?User;
}
