<?php

namespace App\Services\Order;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Data;

readonly class UpdateOrder extends BaseOrderService
{
    /**
     * @param array<string, mixed> $data
     */
    public function execute(string $uuid, array $data): Order|Data|Model|null
    {
        $data = $this->extendData($data);
        $data = \App\DataObjects\Order::from($data);
        return $this->order_repository->updateByUuid($uuid, $data);
    }
}
