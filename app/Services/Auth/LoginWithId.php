<?php

namespace App\Services\Auth;

use App\Dtos\User;
use App\Repositories\Interfaces\JwtTokenRepositoryInterface;
use App\Repositories\UserRepository;
use App\Services\Jwt\GenerateToken;
use Illuminate\Support\Facades\Auth;

readonly class LoginWithId
{
    public function __construct(
        private JwtTokenRepositoryInterface $jwt_token_repository,
        private UserRepository              $user_repository
    ) {
    }

    public function execute(int $id): ?string
    {
        /** @var \App\Models\User|false $user */
        $user = Auth::onceUsingId($id);
        if ($user) {
            $user_dto = User::make($user->getAttributes());
            $token = app(GenerateToken::class)->execute($user_dto);
            $token_id = $this->jwt_token_repository->createToken($token);
            if (!empty($token_id)) {
                // TODO change to event
                $this->user_repository->updateLastLogin($user->uuid);
                return $token->getTokenValue();
            }
        }
        return null;
    }
}
