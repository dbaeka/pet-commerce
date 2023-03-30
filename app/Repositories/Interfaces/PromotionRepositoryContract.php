<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface PromotionRepositoryContract extends CrudRepositoryContract
{
    /**
     * @return LengthAwarePaginator<Model>
     */
    public function getValidList(): LengthAwarePaginator;

    /**
     * @return LengthAwarePaginator<Model>
     */
    public function getInValidList(): LengthAwarePaginator;
}
