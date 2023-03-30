<?php

namespace App\Services\Auth;

readonly class LoginAdminWithCreds extends BaseLoginWithCreds
{
    /**
     * @param array<string, mixed> $credentials
     * @return string|null
     */
    public function execute(array $credentials): ?string
    {
        $user = $this->loginUser($credentials);
        if ($user && $user->getIsAdmin()) {
            return $this->generateToken($user);
        }
        return null;
    }
}
