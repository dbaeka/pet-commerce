<?php

namespace App\Repositories;

use App\DataObjects\Product;
use App\Repositories\Interfaces\ProductRepositoryContract;
use Spatie\LaravelData\DataCollection;

class ProductRepository extends BaseCrudRepository implements ProductRepositoryContract
{
    protected array $with = ['category', 'brand'];

    public function getListWithIds(array $uuids, array $columns = []): DataCollection
    {
        $query = $this->model::query()->whereIn('uuid', $uuids);
        $data = empty($columns) ? $query->select()->get() : $query->select($columns)->get();
        return Product::collection($data);
    }
}
