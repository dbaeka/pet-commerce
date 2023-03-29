<?php

namespace App\Repositories;

use App\Dtos\Order as OrderDto;
use App\Models\Order;
use App\Models\User;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @extends BaseCrudRepository<Order, OrderDto>
 */
class OrderRepository extends BaseCrudRepository implements OrderRepositoryInterface
{
    protected array $with = ['user', 'payment', 'order_status'];

    /**
     * @param string $uuid
     * @return LengthAwarePaginator<Order|Model>
     */
    public function getUserOrders(string $uuid): LengthAwarePaginator
    {
        $user = User::query()->where('uuid', $uuid)->first();
        if ($user) {
            $query = $this->model::query()->with(['user', 'payment', 'order_status'])->whereBelongsTo($user);

            return $this->withPaginate($query);
        }
        throw new ModelNotFoundException();
    }

    /**
     * @return LengthAwarePaginator<Order|Model>
     */
    public function getListForUserUuid(string $uuid): LengthAwarePaginator
    {
        $query = $this->model::query()->where('user_uuid', $uuid);
        $query = $this->withRelations($query);

        return $this->withPaginate($query);
    }
}
