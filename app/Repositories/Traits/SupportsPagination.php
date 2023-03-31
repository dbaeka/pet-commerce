<?php

namespace App\Repositories\Traits;

use Carbon\Carbon;
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
        $filters = request()->except(['limit', 'sort_by', 'page', 'desc', 'valid', 'fixed_range', 'date_range']);

        $query = $query->orderBy($sort_by, $desc ? 'desc' : 'asc');
        $query = $query->where(function (Builder $query) use ($filters) {
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

        $query = $this->handleFixedRange($query);

        $query = $this->handleDateRange($query);

        return $query->paginate($limit);
    }

    /**
     * @param Builder<Model> $query
     * @return Builder<Model>
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function handleFixedRange(Builder $query): Builder
    {
        if (request()->has('fixed_range')) {
            $range = request()->get('fixed_range');
            $from = $to = null;
            switch ($range) {
                case 'today':
                    $from = now()->startOfDay();
                    $to = now()->endOfDay();
                    break;
                case 'monthly':
                    $from = now()->startOfMonth();
                    $to = now()->endOfMonth();
                    break;
                case 'yearly':
                    $from = now()->startOfYear();
                    $to = now()->endOfYear();
            }
            $query->whereBetween('created_at', [$from, $to]);
        }
        return $query;
    }

    /**
     * @param Builder<Model> $query
     * @return Builder<Model>
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function handleDateRange(Builder $query): Builder
    {
        if (request()->has('date_range')) {
            $date_range = request()->get('date_range');
            $from = Carbon::parse($date_range['from']);
            $to = Carbon::parse($date_range['to']);
            $query->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()]);
        }
        return $query;
    }
}
