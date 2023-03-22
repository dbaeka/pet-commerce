<?php

namespace App\Casts;

use App\Values\Product;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use InvalidArgumentException;

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
            return new Product(...$product);
        });
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Collection<int, Product>|array<int, Product> $value
     * @param array<string, mixed> $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): false|string
    {
        if (is_array($value)) {
            $value = collect($value);
        }
        if (!$value instanceof Collection || !$value->every(fn ($item) => $item instanceof Product)) {
            throw new InvalidArgumentException('The given value is not a Collection<int, Product> instance.');
        }
        return json_encode($value);
    }
}
