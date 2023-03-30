<?php

namespace App\Repositories;

use App\DataObjects\Token;
use App\DataObjects\User;
use App\Models\JwtToken;
use App\Repositories\Interfaces\JwtTokenRepositoryContract;
use Illuminate\Database\Eloquent\Builder;

class JwtTokenRepository implements JwtTokenRepositoryContract
{
    public function createToken(Token $token): ?int
    {
        /** @var JwtToken|null $jwt_token */
        $jwt_token = JwtToken::query()->updateOrCreate(
            ['unique_id' => $token->unique_id],
            [
                'user_uuid' => $token->user_uuid,
                'unique_id' => $token->unique_id,
                'token_title' => $token->token_title,
                'restrictions' => $token->restrictions,
                'permissions' => $token->permissions,
                'expires_at' => $token->expires_at,
            ]
        );
        return $jwt_token?->id;
    }

    public function updateTokenLastUsed(string $unique_id): bool
    {
        $update = $this->tokenFromUniqueId($unique_id)->update([
            'last_used_at' => now()
        ]);
        return $update == 1;
    }

    /**
     * @param string $unique_id
     * @return Builder<JwtToken>
     */
    private function tokenFromUniqueId(string $unique_id): Builder
    {
        return JwtToken::query()->where('unique_id', $unique_id);
    }

    public function deleteToken(string $unique_id): bool
    {
        return $this->tokenFromUniqueId($unique_id)->delete();
    }

    public function getUserByUniqueId(string $unique_id): ?User
    {
        $token = $this->tokenFromUniqueId($unique_id)->with('user')->first();

        if ($token) {
            return User::from($token->user);
        }
        return null;
    }

    public function checkTokenExists(string $unique_id): bool
    {
        return $this->tokenFromUniqueId($unique_id)->exists();
    }
}
