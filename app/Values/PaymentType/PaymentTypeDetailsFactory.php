<?php

namespace App\Values\PaymentType;

use App\Enums\PaymentType;

class PaymentTypeDetailsFactory
{
    /**
     * @param PaymentType $type
     * @param array<string, mixed> $details
     * @return PaymentTypeDetails
     */
    public static function make(PaymentType $type, array $details): PaymentTypeDetails
    {
        return match ($type) {
            PaymentType::BANK_TRANSFER => BankTransferDetails::fromArray($details, $type),
            PaymentType::CASH_ON_DELIVERY => CashOnDeliveryDetails::fromArray($details, $type),
            PaymentType::CREDIT_CARD => CreditCardDetails::fromArray($details, $type),
        };
    }
}
