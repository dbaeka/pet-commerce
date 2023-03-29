<?php

namespace App\Values;

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

final class Address extends BaseValueObject
{
    public function __construct(
        public string $shipping,
        public string $billing
    ) {
    }
}
