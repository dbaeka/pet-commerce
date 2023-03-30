<?php

namespace App\Repositories\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @template T of Model
 */
interface SupportsPaginationTraitContract
{
    /**
     * @param Builder<T> $query
     * @return LengthAwarePaginator<Model|T>
     */
    public function withPaginate(Builder $query): LengthAwarePaginator;
}
