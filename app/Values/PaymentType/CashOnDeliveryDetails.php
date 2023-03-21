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

    public function toArray(): array
    {
        return [
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "address" => $this->address,
        ];
    }
}
