<?php

namespace App\Repositories\Interfaces;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface OrderRepositoryContract
{
    /**
     * @param string $uuid
     * @return LengthAwarePaginator<Order|Model>
     */
    public function getUserOrders(string $uuid): LengthAwarePaginator;
}
