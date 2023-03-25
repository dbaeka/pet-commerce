<?php

namespace App\Services;

use App\Repositories\Interfaces\JwtTokenRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\Interfaces\JwtTokenProviderInterface;

readonly class UserService
{
    private JwtTokenProviderInterface $jwt_service;
    private JwtTokenRepositoryInterface $jwt_token_repository;
    private UserRepositoryInterface $user_repository;

    public function __construct()
    {
        $this->jwt_service = app(JwtTokenProviderInterface::class);
        $this->jwt_token_repository = app(JwtTokenRepositoryInterface::class);
        $this->user_repository = app(UserRepositoryInterface::class);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>|null
     */
    public function createAdmin(array $data): ?array
    {
        $data['is_admin'] = true;
        $user = $this->user_repository->createUser($data);
        if ($user) {
            $token = $this->jwt_service->generateToken($user);
            $token_id = $this->jwt_token_repository->createToken($token);
            if (!empty($token_id)) {
                return array_merge([
                    'token' => $token->getTokenValue()
                ], $user->toArray());
            }
        }

        return null;
    }
}