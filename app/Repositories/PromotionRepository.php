<?php

namespace App\Repositories;

use App\Repositories\Interfaces\PromotionRepositoryContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PromotionRepository extends BaseCrudRepository implements PromotionRepositoryContract
{
    public function getValidList(): LengthAwarePaginator
    {
        $query = $this->model::query()->whereDate('valid_to', '>=', now());
        $query = $this->withRelations($query);

        return $this->withPaginate($query);
    }


    public function getInValidList(): LengthAwarePaginator
    {
        $query = $this->model::query()->whereDate('valid_to', '<', now());
        $query = $this->withRelations($query);

        return $this->withPaginate($query);
    }
}
