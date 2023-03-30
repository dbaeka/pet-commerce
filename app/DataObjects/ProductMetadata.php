<?php

namespace App\DataObjects;

use Spatie\LaravelData\Data;

/**
 *
 * @OA\Schema(
 *    schema="ProductMetadata",
 *    description="ProductItem Metadata",
 *    required={"brand", "image"},
 *    @OA\Property(
 *     property="brand",
 *     type="string",
 *     description="ProductItem brand uuid",
 *    ),
 *     @OA\Property(
 *     property="image",
 *     type="string",
 *     description="ProductItem image uuid",
 *    )
 * )
 */
class ProductMetadata extends Data
{
    public string $brand;
    public string $image;
}
