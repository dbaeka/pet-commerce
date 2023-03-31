<?php

namespace App\Services\Order;

use App\DataObjects\ProductItem;
use App\Models\User;
use App\Repositories\Interfaces\OrderRepositoryContract;
use App\Repositories\Interfaces\ProductRepositoryContract;
use Auth;
use Illuminate\Support\Collection;

abstract readonly class BaseOrderService
{
    public function __construct(
        private ProductRepositoryContract $product_repository,
        protected OrderRepositoryContract $order_repository
    ) {
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    final protected function extendData(array $data): array
    {
        /** @var User $user */
        $user = Auth::user();
        $data['user_uuid'] = $user->uuid;
        /** @var array<string, mixed> $products */
        $products = $data['products'];
        $data['products'] = $this->appendProductDetails($products);
        $data['amount'] = $this->getAmount($data['products']);
        $data['delivery_fee'] = $this->getDeliveryFee($data['amount']);
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

    private function getDeliveryFee(float $amount): float
    {
        return $amount > 500 ? 0 : 15;
    }
}
