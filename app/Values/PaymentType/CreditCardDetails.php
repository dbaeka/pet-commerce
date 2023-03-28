<?php

namespace App\Values\PaymentType;

/**
 * @OA\Schema(
 *    schema="CreditCardDetails",
 *    type="object",
 *    required={"number", "cvv", "holder_name"},
 *    @OA\Property(
 *     property="number",
 *     type="string",
 *     description="Card number",
 *    ),
 *    @OA\Property(
 *     property="cvv",
 *     type="string",
 *     description="Card CVV",
 *    ),
 *    @OA\Property(
 *     property="holder_name",
 *     type="string",
 *     description="Card holder name",
 *    ),
 *    @OA\Property(
 *     property="expiry_date",
 *     type="string",
 *     description="Card expiry (MM/YY)",
 *     pattern="^\d{2}/\d{2}$"
 *    ),
 * )
 */

class CreditCardDetails extends PaymentTypeDetails
{
    public function __construct(
        public readonly string $number,
        public readonly string $cvv,
        public readonly string $holder_name,
        public readonly string $expiry_date,
    ) {
        parent::__construct();
    }
}
