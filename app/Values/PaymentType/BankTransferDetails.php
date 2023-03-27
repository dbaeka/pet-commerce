<?php

namespace App\Values\PaymentType;

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
