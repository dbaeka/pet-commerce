<?php

namespace App\Services\Auth;

use App\DataObjects\User;
use App\Events\UserLoggedIn;
use App\Repositories\Interfaces\JwtTokenRepositoryContract;
use App\Services\Jwt\GenerateToken;
use Illuminate\Support\Facades\Auth;

readonly class LoginWithId
{
    public function __construct(
        private JwtTokenRepositoryContract $jwt_token_repository,
    ) {
    }

    public function execute(int $id): ?string
    {
        /** @var \App\Models\User|false $user */
        $user = Auth::onceUsingId($id);
        if ($user) {
            $user_dto = User::from($user);
            $token = app(GenerateToken::class)->execute($user_dto);
            $token_id = $this->jwt_token_repository->createToken($token);
            if (!empty($token_id)) {
                UserLoggedIn::dispatch($user->uuid);
                return $token->getAdditionalData()['token_value'];
            }
        }
        return null;
    }
}
