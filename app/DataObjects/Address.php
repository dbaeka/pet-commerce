<?php

namespace App\DataObjects;

use Spatie\LaravelData\Data;

/**
 *
 * @OA\Schema(
 *    schema="Address",
 *    description="Billing and Shipping address",
 *    required={"shipping", "billing"},
 *    @OA\Property(
 *     property="shipping",
 *     type="string",
 *     description="Shipping address",
 *    ),
 *     @OA\Property(
 *     property="billing",
 *     type="string",
 *     description="Billing address",
 *    )
 * )
 */
class Address extends Data
{
    public string $shipping;
    public string $billing;
}
