<?php

namespace App\Repositories;

use App\Repositories\Interfaces\ProductRepositoryContract;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository extends BaseCrudRepository implements ProductRepositoryContract
{
    protected array $with = ['category', 'brand'];


    public function getListWithIds(array $uuids, array $columns = []): Collection|array
    {
        $query = $this->model::query()->whereIn('uuid', $uuids);
        return empty($columns) ? $query->get() : $query->get($columns);
    }
}
