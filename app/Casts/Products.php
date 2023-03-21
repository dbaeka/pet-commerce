<?php

namespace App\Casts;

use App\Values\Product;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @implements CastsAttributes<Collection<int, Product>, Collection<int, Product>>
 */
class Products implements CastsAttributes
{
    /**
     * Cast the given value.
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array<string, mixed> $attributes
     * @return Collection<int, Product>
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): Collection
    {
        /** @var array<int, mixed> $products */
        $products = json_decode($value, true);
        return collect($products)->map(function ($product) {
            return new Product(
                $product['product'],
                $product['quantity']
            );
        });
    }

    /**
     * Prepare the given value for storage.
     *
     * @param array<string, mixed> $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): false|string
    {
        return json_encode($value);
    }
}
