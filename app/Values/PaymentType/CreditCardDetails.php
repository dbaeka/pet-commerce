<?php

namespace App\Values\PaymentType;

use Carbon\CarbonImmutable;

class CreditCardDetails extends PaymentTypeDetails
{
    public readonly CarbonImmutable $expire_date;

    public function __construct(
        public readonly string $number,
        public readonly string $ccv,
        public readonly string $holder_name,
        string                 $expire_date,
    ) {
        parent::__construct();
        $this->expire_date = new CarbonImmutable($expire_date);
    }

    public function toArray(): array
    {
        return [
            "holder_name" => $this->holder_name,
            "number" => $this->number,
            "ccv" => $this->ccv,
            "expire_date" => $this->expire_date->format('Y-m-d'),
        ];
    }
}
