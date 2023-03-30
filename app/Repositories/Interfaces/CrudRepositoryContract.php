<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Data;

interface CrudRepositoryContract
{
    /**
     * Create a model
     *
     * @param array<string, mixed> $data
     * @return Data|Model|null
     */
    public function create(array $data): Model|Data|null;

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
     * @return Data|Model|null
     */
    public function findByUuid(string $uuid): Model|Data|null;

    /**
     * Update by uuid
     *
     * @param string $uuid
     * @param array<string, mixed> $data
     * @return Data|null
     */
    public function updateByUuid(string $uuid, array $data): Model|Data|null;
}
