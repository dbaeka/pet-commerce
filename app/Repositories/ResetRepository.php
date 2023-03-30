<?php

namespace App\Repositories;

use App\Repositories\Interfaces\ResetRepositoryContract;
use DB;
use Illuminate\Database\Query\Builder;

class ResetRepository implements ResetRepositoryContract
{
    public function checkTokenExists(string $email, string $token): bool
    {
        return $this->getBuilder()->where('email', $email)->where('token', $token)->exists();
    }

    private function getBuilder(): Builder
    {
        return DB::table('password_resets');
    }

    public function addResetToken(string $email, string $token): bool
    {
        return $this->getBuilder()->updateOrInsert(['email' => $email], [
            'email' => $email,
            'token' => $token,
            'created_at' => now()
        ]);
    }

    public function deleteToken(string $email): bool
    {
        return $this->getBuilder()->where('email', $email)->delete() > 0;
    }
}
