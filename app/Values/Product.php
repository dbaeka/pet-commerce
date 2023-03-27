<?php

namespace App\Values;

final class Product extends BaseValueObject
{
    public function __construct(
        public string $product,
        public int    $quantity,
        public string $uuid,
        public float  $price
    ) {
    }
}
