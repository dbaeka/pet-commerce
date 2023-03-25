<?php

namespace App\Repositories\Interfaces;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    /**
     * @param string $uuid
     * @return LengthAwarePaginator<Order>
     */
    public function getUserOrders(string $uuid): LengthAwarePaginator;
}
