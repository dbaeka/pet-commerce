<?php

namespace App\Services\Auth;

use App\Services\Jwt\InvalidateToken;
use Illuminate\Support\Facades\Auth;

readonly class LogoutUser
{
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
