<?php

namespace App\Services\Auth;

readonly class LoginUserWithCreds extends BaseLoginWithCreds
{
    /**
     * @param array<string, mixed> $credentials
     * @return string|null
     */
    public function execute(array $credentials): ?string
    {
        $user = $this->loginUser($credentials);
        if ($user && !$user->is_admin) {
            return $this->generateToken($user);
        }
        return null;
    }
}
