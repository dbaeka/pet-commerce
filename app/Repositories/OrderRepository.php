<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\User;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Traits\SupportsPagination;
use App\Repositories\Traits\SupportsPaginationTraitInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @implements SupportsPaginationTraitInterface<Order>
 */
class OrderRepository implements OrderRepositoryInterface, SupportsPaginationTraitInterface
{
    use SupportsPagination;


    /**
     * @param string $uuid
     * @return LengthAwarePaginator<Order>
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getUserOrders(string $uuid): LengthAwarePaginator
    {
        $user = User::query()->where('uuid', $uuid)->first();
        if ($user) {
            $query = Order::query()->with(['user', 'payment', 'order_status'])->whereBelongsTo($user);

            return $this->withPaginate($query);
        }
        throw new ModelNotFoundException();
    }
}
