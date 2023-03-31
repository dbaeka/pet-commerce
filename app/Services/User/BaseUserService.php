<?php

namespace App\Services\User;

use App\DataObjects\User;
use App\Repositories\Interfaces\JwtTokenRepositoryContract;
use App\Repositories\Interfaces\UserRepositoryContract;
use App\Services\Jwt\GenerateToken;
use Hash;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

abstract readonly class BaseUserService
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
    final protected function storeUser(array $data): ?User
    {
        if ($this->user_repository->findUserByEmail($data['email'])) {
            throw new UnprocessableEntityHttpException('Email already exists');
        }
        $data['is_marketing'] = get_bool(data_get($data, 'is_marketing'));
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
}
