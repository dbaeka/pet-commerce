<?php

namespace App\Values;

final class Address extends BaseValueObject
{
    public function __construct(
        public string $shipping,
        public string $billing
    ) {
    }
}
