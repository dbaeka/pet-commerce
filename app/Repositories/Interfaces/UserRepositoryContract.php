<?php

namespace App\Repositories\Interfaces;

use App\Dtos\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface UserRepositoryContract extends CrudRepositoryContract
{
    public function updateLastLogin(string $uuid): bool;

    /**
     * @return LengthAwarePaginator<\App\Models\User|Model>
     */
    public function getNonAdminUsers(): LengthAwarePaginator;

    public function findUserByEmail(string $email): ?User;
}
