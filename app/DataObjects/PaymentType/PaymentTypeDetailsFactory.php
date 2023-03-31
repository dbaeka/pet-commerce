<?php

namespace App\DataObjects\PaymentType;

use App\Enums\PaymentType;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use ValueError;

class PaymentTypeDetailsFactory
{
    /**
     * @param PaymentType|string $type
     * @param array<string, scalar> $details
     */
    public static function make(PaymentType|string $type, array $details): BasePaymentDetails
    {
        if (is_string($type)) {
            try {
                $type = PaymentType::from($type);
            } catch (ValueError) {
                throw new UnprocessableEntityHttpException();
            }
        }
        return match ($type) {
            PaymentType::BANK_TRANSFER => BankTransferDetails::from($details),
            PaymentType::CASH_ON_DELIVERY => CashOnDeliveryDetails::from($details),
            PaymentType::CREDIT_CARD => CreditCardDetails::from($details),
        };
    }
}
