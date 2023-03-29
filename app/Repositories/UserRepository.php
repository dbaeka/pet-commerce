<?php

namespace App\Repositories;

use App\Dtos\User as UserDto;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends  BaseCrudRepository<User, UserDto>
 */
class UserRepository extends BaseCrudRepository
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
        return $user ? UserDto::make($user->getAttributes()) : null;
    }
}
