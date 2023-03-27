<?php

namespace App\Repositories;

use App\Dtos\User as UserDto;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Traits\SupportsPagination;
use App\Repositories\Traits\SupportsPaginationTraitInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @implements SupportsPaginationTraitInterface<User>
 */
class UserRepository implements UserRepositoryInterface, SupportsPaginationTraitInterface
{
    use SupportsPagination;

    public function createUser(array $data): ?UserDto
    {
        /** @var User|null $user */
        $user = User::query()->create($data);
        return $user ? UserDto::make($user->getAttributes()) : null;
    }


    public function updateLastLogin(string $uuid): bool
    {
        $update = $this->forUserByUuid($uuid)->update([
            'last_login_at' => now()
        ]);
        return $update == 1;
    }

    /**
     * @return LengthAwarePaginator<User>
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function getNonAdminUsers(): LengthAwarePaginator
    {
        $query = User::query()->where('is_admin', false);

        return $this->withPaginate($query);
    }

    public function deleteUserByUuid(string $uuid): bool
    {
        return $this->forUserByUuid($uuid)->delete();
    }

    /**
     * @param string $uuid
     * @return Builder<User>
     */
    private function forUserByUuid(string $uuid): Builder
    {
        return User::query()->where('uuid', $uuid);
    }

    public function findUserByUuid(string $uuid): ?UserDto
    {
        /** @var User|null $user */
        $user = $this->forUserByUuid($uuid)->first();
        return $user ? UserDto::make($user->getAttributes()) : null;
    }


    public function editUserByUuid(string $uuid, array $data): ?UserDto
    {
        /** @var User $user */
        $user = $this->forUserByUuid($uuid)->first();
        $updated = $user->update($data);
        return $updated ? UserDto::make($user->getAttributes()) : null;
    }

    public function findUserByEmail(string $email): ?UserDto
    {
        /** @var User|null $user */
        $user = User::query()->where('email', $email)->first();
        return $user ? UserDto::make($user->getAttributes()) : null;
    }
}
