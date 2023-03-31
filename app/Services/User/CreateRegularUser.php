<?php

namespace App\Services\User;

use App\DataObjects\User;

readonly class CreateRegularUser extends BaseUserService
{
    /**
     * @param array<string, mixed> $data
     */
    public function execute(array $data): ?User
    {
        $data['is_admin'] = false;
        return $this->storeUser($data);
    }
}
