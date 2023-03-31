<?php

namespace App\DataObjects\Casts;

use App\DataObjects\PaymentType\BasePaymentDetails;
use App\DataObjects\PaymentType\PaymentTypeDetailsFactory;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\DataProperty;

class PaymentDetails implements Cast
{
    /**
     * @param DataProperty $property
     * @param mixed $value
     * @param array<string, mixed> $context
     * @return BasePaymentDetails
     */
    public function cast(DataProperty $property, mixed $value, array $context): BasePaymentDetails
    {
        return PaymentTypeDetailsFactory::make($context['type'], $value);
    }
}
