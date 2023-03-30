<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\OrderRepositoryContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderRepository extends BaseCrudRepository implements OrderRepositoryContract
{
    protected array $with = ['user', 'payment', 'order_status'];

    public function getUserOrders(string $uuid): LengthAwarePaginator
    {
        $user = User::query()->where('uuid', $uuid)->first();
        if ($user) {
            $query = $this->model::query()->with(['user', 'payment', 'order_status'])->whereBelongsTo($user);

            return $this->withPaginate($query);
        }
        throw new ModelNotFoundException();
    }

    public function getListForUserUuid(string $uuid): LengthAwarePaginator
    {
        $query = $this->model::query()->where('user_uuid', $uuid);
        $query = $this->withRelations($query);

        return $this->withPaginate($query);
    }

    public function getShippedList(): LengthAwarePaginator
    {
        $query = $this->model::query()->whereNotNull('shipped_at');
        $query = $this->withRelations($query);

        return $this->withPaginate($query);
    }
}
