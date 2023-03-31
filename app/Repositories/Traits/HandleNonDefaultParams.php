<?php

namespace App\Repositories\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class HandleNonDefaultParams
{
    /**
     * @param Builder<Model> $query
     * @return Builder<Model>
     */
    public function handle(Builder $query): Builder
    {
        $query = $this->handleFixedRange($query);

        return $this->handleDateRange($query);
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
