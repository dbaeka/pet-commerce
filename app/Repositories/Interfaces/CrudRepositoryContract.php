<?php

namespace App\Repositories\Interfaces;

use App\Dtos\BaseDto;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface CrudRepositoryContract
{
    /**
     * Create a model
     *
     * @param array<string, mixed> $data
     * @return BaseDto|Model|null
     */
    public function create(array $data): Model|BaseDto|null;

    /**
     * Get list paginated list of model
     *
     * @return LengthAwarePaginator<Model>
     */
    public function getList(): LengthAwarePaginator;

    /**
     * Delete model by uuid
     *
     * @param string $uuid
     * @return bool
     */
    public function deleteByUuid(string $uuid): bool;

    /**
     * Find a model by uuid
     *
     * @param string $uuid
     * @return BaseDto|Model|null
     */
    public function findByUuid(string $uuid): Model|BaseDto|null;

    /**
     * Update by uuid
     *
     * @param string $uuid
     * @param array<string, mixed> $data
     * @return BaseDto|null
     */
    public function updateByUuid(string $uuid, array $data): Model|BaseDto|null;
}
