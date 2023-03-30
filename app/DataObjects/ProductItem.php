<?php

namespace App\DataObjects;

use Spatie\LaravelData\Data;

/**
 *
 * @OA\Schema(
 *    schema="ProductValueRequest",
 *    description="ProductItem uuid and quantity",
 *    required={"uuid", "quantity"},
 *    @OA\Property(
 *     property="uuid",
 *     type="string",
 *     description="ProductItem uuid",
 *    ),
 *     @OA\Property(
 *     property="quantity",
 *     type="integer",
 *     description="ProductItem quantity",
 *    )
 * )
 */
class ProductItem extends Data
{
    public int $quantity;
    public string $uuid;
    public float $price;
    public ?string $product;
}
