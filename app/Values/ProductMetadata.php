<?php

namespace App\Values;

/**
 *
 * @OA\Schema(
 *    schema="ProductMetadata",
 *    description="Product Metadata",
 *    required={"brand", "image"},
 *    @OA\Property(
 *     property="brand",
 *     type="string",
 *     description="Product brand uuid",
 *    ),
 *     @OA\Property(
 *     property="image",
 *     type="string",
 *     description="Product image uuid",
 *    )
 * )
 */
final class ProductMetadata extends BaseValueObject
{
    public function __construct(
        public string $brand,
        public string $image,
    ) {
    }
}
