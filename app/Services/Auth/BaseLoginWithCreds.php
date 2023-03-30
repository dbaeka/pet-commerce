<?php

namespace App\Services\Auth;

use App\Dtos\User;
use App\Repositories\Interfaces\JwtTokenRepositoryContract;
use App\Repositories\UserRepository;
use App\Services\Jwt\GenerateToken;
use Illuminate\Support\Facades\Auth;

abstract readonly class BaseLoginWithCreds
{
    private JwtTokenRepositoryContract $jwt_token_repository;
    private UserRepository $user_repository;

    public function __construct()
    {
        $this->jwt_token_repository = app(JwtTokenRepositoryContract::class);
        $this->user_repository = app(UserRepository::class);
    }

    private function userDtoFromModel(): User
    {
        /** @var array<string, mixed> $user_data */
        $user_data = Auth::user()?->getAttributes();
        return User::make($user_data);
    }

    protected function generateToken(User $user): ?string
    {
        $token = app(GenerateToken::class)->execute($user);
        $token_id = $this->jwt_token_repository->createToken($token);
        if (!empty($token_id)) {
            // TODO change to event
            $this->user_repository->updateLastLogin($user->uuid);
            return $token->getTokenValue();
        }
        return null;
    }

    /**
     * @param array<string, mixed> $credentials
     * @return User|null
     */
    protected function loginUser(array $credentials): ?User
    {
        if (Auth::attempt($credentials)) {
            return $this->userDtoFromModel();
        }
        return null;
    }
}
