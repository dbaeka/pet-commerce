<?php

namespace App\Values;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * @implements Arrayable<string, mixed>
 */
final readonly class Product implements Arrayable, JsonSerializable
{
    public function __construct(
        public string $product,
        public int    $quantity
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            "product" => $this->product,
            "quantity" => $this->quantity,
        ];
    }
}
