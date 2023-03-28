<?php

namespace App\Values\PaymentType;

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
class BankTransferDetails extends PaymentTypeDetails
{
    public function __construct(
        public readonly string $swift,
        public readonly string $iban,
        public readonly string $name,
    ) {
        parent::__construct();
    }
}
