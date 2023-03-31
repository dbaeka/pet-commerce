<?php

namespace App\Services;

use App\DataObjects\ProductItem;
use App\Models\Order;
use App\Models\User;
use App\Repositories\Interfaces\OrderRepositoryContract;
use App\Repositories\Interfaces\ProductRepositoryContract;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

readonly class OrderService
{
    private OrderRepositoryContract $order_repository;
    private ProductRepositoryContract $product_repository;

    public function __construct()
    {
        $this->order_repository = app(OrderRepositoryContract::class);
        $this->product_repository = app(ProductRepositoryContract::class);
    }

    /**
     * @param array<string, mixed> $data
     * @return Order|Model|null
     */
    public function createOrder(array $data): Data|Model|null
    {
        $data = $this->extendData($data);
        $data = \App\DataObjects\Order::from($data);
        return $this->order_repository->create($data);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function extendData(array $data): array
    {
        /** @var User $user */
        $user = Auth::user();
        $data['user_uuid'] = $user->uuid;
        /** @var array<string, mixed> $products */
        $products = $data['products'];
        $data['products'] = $this->appendProductDetails($products);
        $data['amount'] = $this->getAmount($data['products']);
        return $data;
    }

    /**
     * @param array<string, mixed> $products
     * @return array<string, mixed>
     */
    private function appendProductDetails(array $products): array
    {
        $product_uuids = collect($products)->pluck('uuid')->toArray();
        /** @var Collection<int, \App\Models\Product> $products_info */
        $products_info = $this->product_repository->getListWithIds(
            $product_uuids,
            ['uuid', 'title', 'price']
        );
        $products_info = collect($products_info)->keyBy('uuid');
        return array_map(function (array $value) use ($products_info) {
            $product = $products_info->get($value['uuid']);
            return ProductItem::from([
                'quantity' => $value['quantity'],
                'uuid' => $value['uuid'],
                'price' => $product['price'],
                'product' => $product['title']
            ]);
        }, $products);
    }

    /**
     * @param array<string, mixed> $products
     * @return float
     */
    private function getAmount(array $products): float
    {
        return collect($products)->sum(fn (ProductItem $value) => round($value->price * $value->quantity, 2));
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateOrder(string $uuid, array $data): Order|Data|Model|null
    {
        $data = $this->extendData($data);
        $data = \App\DataObjects\Order::from($data);
        return $this->order_repository->updateByUuid($uuid, $data);
    }
}
