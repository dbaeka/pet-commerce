<?php

namespace App\DataObjects\PaymentType;

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
class CreditCardDetails extends BasePaymentDetails
{
    public function __construct(
        public string $number,
        public string $cvv,
        public string $holder_name,
        public string $expiry_date,
    ) {
    }
}
