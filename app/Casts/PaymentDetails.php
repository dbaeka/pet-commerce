<?php

namespace App\Casts;

use App\DataObjects\PaymentType\BankTransferDetails;
use App\DataObjects\PaymentType\CashOnDeliveryDetails;
use App\DataObjects\PaymentType\CreditCardDetails;
use App\DataObjects\PaymentType\PaymentTypeDetailsFactory;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements CastsAttributes<BankTransferDetails|CreditCardDetails|CashOnDeliveryDetails,string|false>
 */
class PaymentDetails implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param array<string, mixed> $attributes
     */
    public function get(?Model $model, string $key, mixed $value, array $attributes): BankTransferDetails|CashOnDeliveryDetails|CreditCardDetails
    {
        $details = json_decode($value, true);
        return PaymentTypeDetailsFactory::make($attributes['type'], $details);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param array<string, mixed> $attributes
     */
    public function set(?Model $model, string $key, mixed $value, array $attributes): string|false
    {
        return json_encode($value);
    }
}
