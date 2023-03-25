<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @template T of Model
 */
interface SupportsPaginationTraitInterface
{
    /**
     * @param Builder<T> $query
     * @return LengthAwarePaginator<T>
     */
    public function withPaginate(Builder $query): LengthAwarePaginator;
}
