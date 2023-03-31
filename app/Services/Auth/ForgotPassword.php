<?php

namespace App\Services\Auth;

use App\Repositories\Interfaces\ResetRepositoryContract;
use App\Repositories\Interfaces\UserRepositoryContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

readonly class ForgotPassword
{
    public function __construct(
        private UserRepositoryContract $user_repository,
        private ResetRepositoryContract $reset_repository
    ) {
    }

    public function execute(string $email): string
    {
        $user = $this->user_repository->findUserByEmail($email);
        if (empty($user)) {
            throw new ModelNotFoundException();
        }

        if ($user->is_admin) {
            throw new UnauthorizedException();
        }

        $token = hash('sha256', strval(now()->timestamp));

        $saved = $this->reset_repository->addResetToken($email, $token);
        if ($saved) {
            return $token;
        }
        throw new UnprocessableEntityHttpException();
    }
}
