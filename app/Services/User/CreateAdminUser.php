<?php

namespace App\Services\User;

use App\DataObjects\User;

readonly class CreateAdminUser extends BaseUserService
{
    /**
     * @param array<string, mixed> $data
     */
    public function execute(array $data): ?User
    {
        $data['is_admin'] = true;
        return $this->storeUser($data);
    }
}
