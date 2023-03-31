<?php

namespace App\Repositories\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
        $desc = request()->boolean('desc');

        $query = $query->orderBy($sort_by, $desc ? 'desc' : 'asc');

        $query = $this->handleFilters($query);

        $query = app(HandleNonDefaultParams::class)->handle($query);

        return $query->paginate($limit);
    }

    /**
     * @param Builder<Model> $query
     * @return Builder<Model>
     */
    private function handleFilters(Builder $query): Builder
    {
        $filters = request()->except(['limit', 'sort_by', 'page', 'desc', 'valid', 'fixed_range', 'date_range']);
        return $query->where(function (Builder $query) use ($filters) {
            foreach ($filters as $key => $value) {
                if (in_array($value, ['true', 'false'])) {
                    $value = filter_var($value, FILTER_VALIDATE_BOOL);
                }
                if (str_ends_with($key, '_at')) {
                    $query->whereDate($key, $value);
                } else {
                    $query->where($key, $value);
                }
            }
        });
    }
}
