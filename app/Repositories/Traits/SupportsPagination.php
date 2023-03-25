<?php

namespace App\Repositories\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

trait SupportsPagination
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    final public function withPaginate(Builder $query): LengthAwarePaginator
    {
        $limit = request()->get('limit', 20);
        $sort_by = request()->get('sort_by', 'id');
        $desc = request()->get('desc', true);
        $filters = request()->except(['limit', 'sort_by', 'page', 'desc']);

        return $query->orderBy($sort_by, $desc ? 'desc' : 'asc')
            ->where(function (Builder $query) use ($filters) {
                foreach ($filters as $key => $value) {
                    $query->where($key, $value);
                }
            })
            ->paginate($limit);
    }
}
