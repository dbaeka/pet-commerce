<?php

namespace App\Services\Auth;

use App\Exceptions\Jwt\InvalidJwtToken;
use App\Services\Jwt\InvalidateToken;
use Illuminate\Support\Facades\Auth;

readonly class LogoutUser
{
    /**
     * @throws InvalidJwtToken
     */
    public function execute(): bool
    {
        Auth::logout();
        $token = request()->bearerToken();
        if ($token) {
            return app(InvalidateToken::class)->execute($token);
        }
        return true;
    }
}
