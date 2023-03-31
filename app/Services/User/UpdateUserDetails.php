<?php

namespace App\Services\User;

use App\DataObjects\User;
use App\Repositories\Interfaces\UserRepositoryContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\LaravelData\Data;

readonly class UpdateUserDetails
{
    public function __construct(
        private UserRepositoryContract $user_repository
    ) {
    }

    /**
     * @param string $uuid
     * @param array<string, mixed> $data
     * @return Data|null
     */
    public function execute(string $uuid, array $data): ?Data
    {
        $user = $this->user_repository->findByUuid($uuid);
        if (empty($user)) {
            throw new ModelNotFoundException();
        }
        $data['is_marketing'] = get_bool(data_get($data, 'is_marketing'));
        $data = User::from($data);
        return $this->user_repository->updateByUuid($uuid, $data);
    }
}
