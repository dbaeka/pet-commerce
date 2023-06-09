<?php

namespace App\Services\Jwt;

use App\Exceptions\Jwt\InvalidJwtToken;

class InvalidateToken extends BaseJwtProvider
{
    /**
     * @throws InvalidJwtToken
     */
    public function execute(string $token): bool
    {
        $parsed_token = $this->parseToken($token);
        if (empty($parsed_token)) {
            throw new InvalidJwtToken();
        }

        $unique_id = $parsed_token->claims()->get('jti');
        return $this->jwt_token_repository->deleteToken($unique_id);
    }
}
