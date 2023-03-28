<?php

namespace App\Repositories\Interfaces;

use App\Dtos\BaseDto;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of Model
 * @template TObj of BaseDto
 */
interface CrudRepositoryInterface
{
    /**
     * Create a model
     *
     * @param array<string, mixed> $data
     * @return TObj|BaseDto|TModel|Model|null
     */
    public function create(array $data);

    /**
     * Get list paginated list of model
     *
     * @return LengthAwarePaginator<TModel>
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
     * @return TObj|BaseDto|TModel|Model|null
     */
    public function findByUuid(string $uuid);

    /**
     * Update by uuid
     *
     * @param string $uuid
     * @param array<string, mixed> $data
     * @return TObj|BaseDto|TModel|null
     */
    public function updateByUuid(string $uuid, array $data);
}
