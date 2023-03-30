<?php

namespace App\Repositories\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

interface SupportsPaginationTraitContract
{
    /**
     * @param Builder<Model> $query
     * @return LengthAwarePaginator<Model>
     */
    public function withPaginate(Builder $query): LengthAwarePaginator;
}
