<?php

namespace App\Repositories\Interfaces;

use App\Dtos\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function updateLastLogin(string $uuid): bool;

    /**
     * @return LengthAwarePaginator<\App\Models\User>
     */
    public function getNonAdminUsers(): LengthAwarePaginator;

    public function findUserByEmail(string $email): ?User;
}
