<?php

namespace App\Services;

use App\Dtos\User;
use App\Repositories\Interfaces\JwtTokenRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\Interfaces\JwtTokenProviderInterface;
use Illuminate\Support\Facades\Auth;

readonly class AuthService
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
     * @param array<string, mixed> $credentials
     * @return string|null
     */
    public function loginAdminUser(array $credentials): ?string
    {
        if (Auth::attempt($credentials)) {
            /** @var array<string, mixed> $user_data */
            $user_data = Auth::user()?->getAttributes();
            $user = User::make($user_data);
            if ($user->is_admin) {
                $token = $this->jwt_service->generateToken($user);
                $token_id = $this->jwt_token_repository->createToken($token);
                if (!empty($token_id)) {
                    // TODO change to event
                    $this->user_repository->updateLastLogin($user->id);
                    return $token->getTokenValue();
                }
            }
        }
        return null;
    }

    public function loginUsingId(int $id): ?string
    {
        if ($user = Auth::onceUsingId($id)) {
            /** @var \App\Models\User $user */
            $user_dto = User::make($user->getAttributes());
            $token = $this->jwt_service->generateToken($user_dto);
            $token_id = $this->jwt_token_repository->createToken($token);
            if (!empty($token_id)) {
                // TODO change to event
                $this->user_repository->updateLastLogin($user->id);
                return $token->getTokenValue();
            }
        }
        return null;
    }


    public function logoutUser(): bool
    {
        Auth::logout();
        $token = request()->bearerToken();
        if ($token) {
            return $this->jwt_service->inValidateToken($token);
        }
        return true;
    }
}
