<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface OrderRepositoryContract extends CrudRepositoryContract
{
    /**
     * @return LengthAwarePaginator<Model>
     */
    public function getUserOrders(string $uuid): LengthAwarePaginator;

    /**
     * @return LengthAwarePaginator<Model>
     */
    public function getListForUserUuid(string $uuid): LengthAwarePaginator;

    /**
     * @return LengthAwarePaginator<Model>
     */
    public function getShippedList(): LengthAwarePaginator;
}
