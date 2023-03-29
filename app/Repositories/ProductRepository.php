<?php

namespace App\Repositories;

use App\Dtos\Product as ProductDto;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends BaseCrudRepository<Product, ProductDto>
 */
class ProductRepository extends BaseCrudRepository
{
    protected array $with = ['category', 'brand'];

    /**
     * @param string[] $uuids
     * @param string[] $columns
     * @return Collection<int, Model>|array<int, Model>
     */
    public function getListWithIds(array $uuids, array $columns = []): Collection|array
    {
        $query = $this->model::query()->whereIn('uuid', $uuids);
        return empty($columns) ? $query->get() : $query->get($columns);
    }
}
