<?php

namespace App\Values\PaymentType;

use App\Enums\PaymentType;
use App\Values\BaseValueObject;
use InvalidArgumentException;

/**
 * @phpstan-consistent-constructor
 */
abstract class PaymentTypeDetails extends BaseValueObject
{
    private PaymentType $type;

    public function __construct()
    {
    }

    /**
     * @param array<string, scalar> $data
     * @return static
     */
    final public static function fromArray(array $data, PaymentType $type): static
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
}
