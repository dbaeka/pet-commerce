<?php

namespace App\Values\PaymentType;

use App\Enums\PaymentType;
use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;
use JsonSerializable;

/**
 * @implements Arrayable<string, mixed>
 * @phpstan-consistent-constructor
 */
abstract class PaymentTypeDetails implements JsonSerializable, Arrayable
{
    private PaymentType $type;

    public function __construct()
    {
    }

    /**
     * @param array<string, mixed> $data
     * @param PaymentType $type
     * @return static
     */
    public static function fromArray(array $data, PaymentType $type): static
    {
        foreach ($data as $key => $value) {
            if (!property_exists(static::class, $key)) {
                throw new InvalidArgumentException("Unknown argument $key in data array");
            }
        }
        $obj = new static(...$data);
        $obj->setType($type);
        return $obj;
    }

    private function setType(PaymentType $type): void
    {
        $this->type = $type;
    }

    public function getType(): PaymentType
    {
        return $this->type;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
