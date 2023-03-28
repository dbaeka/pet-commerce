<?php

namespace App\Values\PaymentType;

use App\Enums\PaymentType;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use ValueError;

class PaymentTypeDetailsFactory
{
    /**
     * @param PaymentType|string $type
     * @param array<string, scalar> $details
     * @return PaymentTypeDetails
     */
    public static function make(PaymentType|string $type, array $details): PaymentTypeDetails
    {
        if (is_string($type)) {
            try {
                $type = PaymentType::from($type);
            } catch (ValueError) {
                throw new UnprocessableEntityHttpException();
            }
        }
        return match ($type) {
            PaymentType::BANK_TRANSFER => BankTransferDetails::fromArray($details, $type),
            PaymentType::CASH_ON_DELIVERY => CashOnDeliveryDetails::fromArray($details, $type),
            PaymentType::CREDIT_CARD => CreditCardDetails::fromArray($details, $type),
        };
    }
}
