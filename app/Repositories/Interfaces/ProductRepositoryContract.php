<?php

namespace App\Repositories\Interfaces;

use App\DataObjects\Product;
use Spatie\LaravelData\DataCollection;

interface ProductRepositoryContract extends CrudRepositoryContract
{
    /**
     * @param array<string> $uuids
     * @param array<string> $columns
     * @return DataCollection<int, Product>
     */
    public function getListWithIds(array $uuids, array $columns = []): DataCollection;
}
