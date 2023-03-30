<?php

namespace App\Services\Auth;

use App\Repositories\Interfaces\ResetRepositoryInterface;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

readonly class ForgotPassword
{
    public function __construct(
        private UserRepository           $user_repository,
        private ResetRepositoryInterface $reset_repository
    ) {
    }


    public function execute(string $email): string
    {
        $user = $this->user_repository->findUserByEmail($email);
        if (empty($user)) {
            throw new ModelNotFoundException();
        }

        if ($user->getIsAdmin()) {
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
