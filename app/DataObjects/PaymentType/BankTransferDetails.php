<?php

namespace App\DataObjects\PaymentType;

/**
 * @OA\Schema(
 *    schema="BankTransferDetails",
 *    type="object",
 *    required={"swift", "iban", "name"},
 *    @OA\Property(
 *     property="swift",
 *     type="string",
 *     description="Bank SWIFT",
 *    ),
 *    @OA\Property(
 *     property="iban",
 *     type="string",
 *     description="Bank IBAN",
 *    ),
 *    @OA\Property(
 *     property="name",
 *     type="string",
 *     description="Name on Bank account",
 *    ),
 * )
 */
class BankTransferDetails extends BasePaymentDetails
{
    public function __construct(
        public string $swift,
        public string $iban,
        public string $name,
    ) {
    }
}
