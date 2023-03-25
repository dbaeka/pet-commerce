<?php

namespace App\Repositories\Interfaces;

use App\Dtos\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    /**
     * @param array<string, mixed> $data
     * @return User|null
     */
    public function createUser(array $data): ?User;

    public function updateLastLogin(int $id): bool;

    /**
     * @return LengthAwarePaginator<\App\Models\User>
     */
    public function getNonAdminUsers(): LengthAwarePaginator;

    public function deleteUserByUuid(string $uuid): bool;

    public function findUserByUuid(string $uuid): ?User;

    public function findUserByEmail(string $email): ?User;

    /**
     * @param string $uuid
     * @param array<string, mixed> $data
     * @return User|null
     */
    public function editUserByUuid(string $uuid, array $data): ?User;
}
