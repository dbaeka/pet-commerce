<?php

namespace App\Services\Auth;

use App\DataObjects\User;
use App\Events\UserLoggedIn;
use App\Repositories\Interfaces\JwtTokenRepositoryContract;
use App\Services\Jwt\GenerateToken;
use Illuminate\Support\Facades\Auth;

abstract readonly class BaseLoginWithCreds
{
    private JwtTokenRepositoryContract $jwt_token_repository;

    public function __construct()
    {
        $this->jwt_token_repository = app(JwtTokenRepositoryContract::class);
    }

    protected function generateToken(User $user): ?string
    {
        $token = app(GenerateToken::class)->execute($user);
        $token_id = $this->jwt_token_repository->createToken($token);
        if (!empty($token_id)) {
            /** @var string $uuid */
            $uuid = $user->uuid;
            UserLoggedIn::dispatch($uuid);
            return $token->getAdditionalData()['token_value'];
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

    private function userDtoFromModel(): User
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return User::from($user->makeVisible(['id', 'is_admin']));
    }
}
