<?php

namespace App\Values;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * @implements Arrayable<string, string>
 */
final class Address implements Arrayable, JsonSerializable
{
    public function __construct(
        public string $shipping,
        public string $billing
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            "billing" => $this->shipping,
            "shipping" => $this->billing,
        ];
    }
}
