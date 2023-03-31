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
     * @param Data $data
     * @return Data|null
     */
    public function create(Data $data): ?Data;

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
     * @return Data|null
     */
    public function findByUuid(string $uuid): ?Data;

    /**
     * Update by uuid
     *
     * @param string $uuid
     * @param Data $data
     * @return Data|null
     */
    public function updateByUuid(string $uuid, Data $data): ?Data;
}
