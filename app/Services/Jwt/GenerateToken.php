<?php

namespace App\Services\Jwt;

use App\DataObjects\Token;
use App\DataObjects\User;

class GenerateToken extends BaseJwtProvider
{
    public function execute(User $user): Token
    {
        $config = $this->config;

        $now = now();
        $expires_at = now()->addSeconds($this->expiry_seconds);

        $unique_id = hash('sha256', strval(now()->timestamp));

        $jwt = $config->builder()
            ->issuedBy($this->issuer)
            ->identifiedBy($unique_id)
            ->withClaim('user_uuid', $user->uuid)
            ->issuedAt($now->toDateTimeImmutable())
            ->expiresAt($expires_at->toDateTimeImmutable())
            ->getToken($config->signer(), $config->signingKey());

        $data = [
            'user_uuid' => $user->uuid,
            'unique_id' => $unique_id,
            'token_title' => 'Access Token',
            'expires_at' => $expires_at,
        ];
        return Token::from($data)->additional(['token_value' => $jwt->toString()]);
    }
}
