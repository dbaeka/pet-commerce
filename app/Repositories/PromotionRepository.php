<?php

namespace App\Repositories;

use App\Dtos\Promotion as PromotionDto;
use App\Models\Promotion;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends BaseCrudRepository<Promotion, PromotionDto>
 */
class PromotionRepository extends BaseCrudRepository
{
    /**
     * @return LengthAwarePaginator<Promotion|Model>
     */
    public function getValidList(): LengthAwarePaginator
    {
        $query = $this->model::query()->whereDate('valid_to', '>=', now());
        $query = $this->withRelations($query);

        return $this->withPaginate($query);
    }

    /**
     * @return LengthAwarePaginator<Promotion|Model>
     */
    public function getInValidList(): LengthAwarePaginator
    {
        $query = $this->model::query()->whereDate('valid_to', '<', now());
        $query = $this->withRelations($query);

        return $this->withPaginate($query);
    }
}
