<?php

namespace App\Services\Order;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Data;

readonly class CreateOrder extends BaseOrderService
{
    /**
     * @param array<string, mixed> $data
     * @return Order|Model|null
     */
    public function execute(array $data): Data|Model|null
    {
        $data = $this->extendData($data);
        $data = \App\DataObjects\Order::from($data);
        return $this->order_repository->create($data);
    }
}
