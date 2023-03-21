<?php

namespace App\Enums;

enum PaymentType: string
{
    case CASH_ON_DELIVERY = 'cash_on_delivery';
    case CREDIT_CARD = 'credit_card';
    case BANK_TRANSFER = 'bank_transfer';
}
