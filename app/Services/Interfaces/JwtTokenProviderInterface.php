<?php

namespace App\Services\Interfaces;

use App\Dtos\User;

interface JwtTokenProviderInterface
{
    public function generateToken(User $user): ?string;

    public function validateToken(string $token): bool;

    public function getUserFromToken(string $token): ?User;

    /**
     * @param string $token
     * @return array<string, mixed>
     */
    public function getPayload(string $token): array;

    public function authenticate(string $token): ?User;
}
