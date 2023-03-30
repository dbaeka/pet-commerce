<?php

namespace App\DataObjects\PaymentType;

use Spatie\LaravelData\Data;

/**
 * @OA\Schema(
 *    schema="CashOnDeliveryDetails",
 *    type="object",
 *    required={"first_name", "last_name", "address_line1", "address_line2", "consent",  "text"},
 *    @OA\Property(
 *     property="first_name",
 *     type="string",
 *     description="First name",
 *    ),
 *    @OA\Property(
 *     property="last_name",
 *     type="string",
 *     description="Last name",
 *    ),
 *    @OA\Property(
 *     property="address_line1",
 *     type="string",
 *     description="Address Line 1",
 *    ),
 *    @OA\Property(
 *     property="address_line2",
 *     type="string",
 *     description="Address Line 2",
 *    ),
 *     @OA\Property(
 *     property="ref_code",
 *     type="string",
 *     description="Text reference",
 *    ),
 *     @OA\Property(
 *     property="consent",
 *     type="boolean",
 *     description="Consent",
 *    ),
 * )
 */
class CashOnDeliveryDetails extends Data
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public string $address_line1,
        public string $address_line2,
        public string $ref_code,
        public bool   $consent
    ) {
    }
}
