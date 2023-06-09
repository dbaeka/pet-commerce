<?php

namespace App\Services\Auth;

use App\DataObjects\User;
use App\Repositories\Interfaces\ResetRepositoryContract;
use App\Repositories\Interfaces\UserRepositoryContract;
use Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\UnauthorizedException;

readonly class ResetPassword
{
    public function __construct(
        private UserRepositoryContract  $user_repository,
        private ResetRepositoryContract $reset_repository
    ) {
    }

    /**
     * @param array<string, mixed> $data
     * @return bool
     */
    public function execute(array $data): bool
    {
        $user = $this->user_repository->findUserByEmail($data['email']);
        if (empty($user)) {
            throw new ModelNotFoundException();
        }

        if ($user->is_admin) {
            throw new UnauthorizedException();
        }
        $data['password'] = Hash::make($data['password']);
        $data = User::from($data);
        /** @var string $uuid */
        $uuid = $user->uuid;
        $user = $this->user_repository->updateByUuid($uuid, $data);
        /** @var string $email */
        $email = $data->email;
        $success = $this->reset_repository->deleteToken($email);
        if ($user && $success) {
            return true;
        }
        return false;
    }
}
