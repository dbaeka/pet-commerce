<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ProductRepositoryContract extends CrudRepositoryContract
{
    /**
     * @param array<string> $uuids
     * @param array<string> $columns
     * @return Collection<int, Model>|array<int, Model>
     */
    public function getListWithIds(array $uuids, array $columns = []): Collection|array;
}
