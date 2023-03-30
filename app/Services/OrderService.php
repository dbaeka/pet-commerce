<?php

namespace App\Services;

use App\Dtos\BaseDto;
use App\Models\Order;
use App\Models\User;
use App\Repositories\Interfaces\OrderRepositoryContract;
use App\Repositories\Interfaces\ProductRepositoryContract;
use App\Values\Address;
use App\Values\Product;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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
    public function createOrder(array $data): BaseDto|Model|null
    {
        $data = $this->extendData($data);
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
        $data['address'] = new Address(...$data['address']);
        /** @var array<string, mixed> $products */
        $products = $data['products'];
        $products = collect($products);
        $data['products'] = $this->appendProductDetails($products);
        $data['amount'] = $this->getAmount($data['products']);
        return $data;
    }

    /**
     * @param Collection<string, mixed> $products
     * @return Collection<string, Product>
     */
    private function appendProductDetails(Collection $products): Collection
    {
        $product_uuids = $products->pluck('uuid');
        /** @var Collection<int, \App\Models\Product> $products_info */
        $products_info = $this->product_repository->getListWithIds(
            $product_uuids->toArray(),
            ['uuid', 'title', 'price']
        );
        $products_info = $products_info->keyBy('uuid');
        return $products->map(function (array $value) use ($products_info) {
            /** @var \App\Models\Product $product */
            $product = $products_info->get($value['uuid']);
            return new Product(
                quantity: $value['quantity'],
                uuid: $value['uuid'],
                price: $product->price,
                product: $product->title
            );
        });
    }

    /**
     * @param Collection<string, Product> $products
     * @return float
     */
    private function getAmount(Collection $products): float
    {
        return $products->sum(fn (Product $value) => round($value->price * $value->quantity, 2));
    }

    /**
     * @param array<string, mixed> $data
     * @return Order|BaseDto|null
     */
    public function updateOrder(string $uuid, array $data): Order|BaseDto|null
    {
        $data = $this->extendData($data);
        return $this->order_repository->updateByUuid($uuid, $data);
    }
}
