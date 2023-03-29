<?php

namespace App\Values;

/**
 *
 * @OA\Schema(
 *    schema="ProductValueRequest",
 *    description="Product uuid and quantity",
 *    required={"uuid", "quantity"},
 *    @OA\Property(
 *     property="uuid",
 *     type="string",
 *     description="Product uuid",
 *    ),
 *     @OA\Property(
 *     property="quantity",
 *     type="integer",
 *     description="Product quantity",
 *    )
 * )
 */
final class Product extends BaseValueObject
{
    public function __construct(
        public int    $quantity,
        public string $uuid,
        public float  $price = 0,
        public ?string $product = null,
    ) {
    }
}
