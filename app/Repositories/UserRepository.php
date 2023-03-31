<?php

namespace App\Repositories;

use App\DataObjects\User as UserDto;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class UserRepository extends BaseCrudRepository implements UserRepositoryContract
{
    public function updateLastLogin(string $uuid): bool
    {
        $update = $this->byUuid($uuid)->update([
            'last_login_at' => now()
        ]);
        return $update == 1;
    }

    /**
     * @return LengthAwarePaginator<User|Model>
     */
    public function getNonAdminUsers(): LengthAwarePaginator
    {
        $query = $this->model::query()->where('is_admin', false);

        return $this->withPaginate($query);
    }

    public function findUserByEmail(string $email): ?UserDto
    {
        /** @var User|null $user */
        $user = $this->model::query()->where('email', $email)->first();
        return $user ? UserDto::from($user->makeVisible(['id', 'is_admin'])) : null;
    }
}
