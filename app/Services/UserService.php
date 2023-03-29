<?php

namespace App\Services;

use App\Dtos\User;
use App\Repositories\Interfaces\JwtTokenRepositoryInterface;
use App\Repositories\UserRepository;
use App\Services\Interfaces\JwtTokenProviderInterface;
use Hash;

readonly class UserService
{
    private JwtTokenProviderInterface $jwt_service;
    private JwtTokenRepositoryInterface $jwt_token_repository;
    private UserRepository $user_repository;

    public function __construct()
    {
        $this->jwt_service = app(JwtTokenProviderInterface::class);
        $this->jwt_token_repository = app(JwtTokenRepositoryInterface::class);
        $this->user_repository = app(UserRepository::class);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>|null
     */
    public function createAdmin(array $data): ?array
    {
        $data['is_admin'] = true;
        return $this->storeUser($data);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>|null
     */
    private function storeUser(array $data): ?array
    {
        $data['password'] = Hash::make($data['password']);
        /** @var \App\Models\User|null $user */
        $user = $this->user_repository->create($data);
        if ($user) {
            $user = User::make($user->getAttributes());
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

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>|null
     */
    public function createUser(array $data): ?array
    {
        $data['is_admin'] = false;
        return $this->storeUser($data);
    }
}
