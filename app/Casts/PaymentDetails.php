<?php

namespace App\Casts;

use App\Enums\PaymentType;
use App\Values\PaymentType\PaymentTypeDetails;
use App\Values\PaymentType\PaymentTypeDetailsFactory;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * @implements CastsAttributes<PaymentTypeDetails,PaymentTypeDetails>
 */
class PaymentDetails implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param array<string, mixed> $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): PaymentTypeDetails
    {
        $details = json_decode($value, true);
        $type = PaymentType::from($attributes['type']);
        return PaymentTypeDetailsFactory::make($type, $details);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param array<string, mixed> $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string|false
    {
        if (!$value instanceof PaymentTypeDetails) {
            throw new InvalidArgumentException('The given value is not an PaymentTypeDetails instance.');
        }

        return json_encode($value);
    }
}
