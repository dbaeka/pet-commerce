<?php

namespace App\Values\PaymentType;

class CreditCardDetails extends PaymentTypeDetails
{
    public function __construct(
        public readonly string $number,
        public readonly string $cvv,
        public readonly string $holder_name,
        public readonly string $expire_date,
    ) {
        parent::__construct();
    }
}
