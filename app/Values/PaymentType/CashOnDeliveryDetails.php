<?php

namespace App\Values\PaymentType;

class CashOnDeliveryDetails extends PaymentTypeDetails
{
    public function __construct(
        public readonly string $first_name,
        public readonly string $last_name,
        public readonly string $address,
    ) {
        parent::__construct();
    }
}
