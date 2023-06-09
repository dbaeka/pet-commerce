<?php

namespace App\Services\Jwt;

use App\DataObjects\User;
use App\Events\TokenUsed;
use App\Exceptions\Jwt\InvalidJwtToken;

class AuthenticateWithToken extends BaseJwtProvider
{
    /**
     * @throws InvalidJwtToken
     */
    public function execute(string $token): ?User
    {
        $parsed_token = $this->parseToken($token);
        if (empty($parsed_token)) {
            throw new InvalidJwtToken();
        }

        $unique_id = $parsed_token->claims()->get('jti');
        $jwt_token_exists = $this->jwt_token_repository->checkTokenExists($unique_id);

        if ($jwt_token_exists) {
            TokenUsed::dispatch($unique_id);
        }

        $is_expired = $parsed_token->isExpired(now());
        if ($jwt_token_exists && !$is_expired) {
            return $this->jwt_token_repository->getUserByUniqueId($unique_id);
        }
        if ($is_expired) {
            $this->jwt_token_repository->deleteToken($unique_id);
        }
        return null;
    }
}
