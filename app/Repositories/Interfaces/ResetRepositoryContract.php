<?php

namespace App\Repositories\Interfaces;

interface ResetRepositoryContract
{
    public function addResetToken(string $email, string $token): bool;

    public function deleteToken(string $email): bool;

    public function checkTokenExists(string $email, string $token): bool;
}
