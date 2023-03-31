<?php

namespace App\Services;

use App\DataObjects\User;
use App\Repositories\Interfaces\JwtTokenRepositoryContract;
use App\Repositories\Interfaces\UserRepositoryContract;
use App\Services\Jwt\GenerateToken;
use Hash;

readonly class UserService
{
    private JwtTokenRepositoryContract $jwt_token_repository;
    private UserRepositoryContract $user_repository;

    public function __construct()
    {
        $this->jwt_token_repository = app(JwtTokenRepositoryContract::class);
        $this->user_repository = app(UserRepositoryContract::class);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createAdmin(array $data): ?User
    {
        $data['is_admin'] = true;
        return $this->storeUser($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function storeUser(array $data): ?User
    {
        $data['password'] = Hash::make($data['password']);
        $data = User::from($data);
        $user = $this->user_repository->create($data);
        if ($user) {
            $user = User::from($user);
            $token = app(GenerateToken::class)->execute($user);
            $token_id = $this->jwt_token_repository->createToken($token);
            if (!empty($token_id)) {
                return $user->additional(['token' => $token->getAdditionalData()['token_value']]);
            }
        }
        return null;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createUser(array $data): ?User
    {
        $data['is_admin'] = false;
        return $this->storeUser($data);
    }
}
