<?php

namespace App\DataObjects;

use App\DataObjects\PaymentType\BankTransferDetails;
use App\DataObjects\PaymentType\CashOnDeliveryDetails;
use App\DataObjects\PaymentType\CreditCardDetails;
use App\Enums\PaymentType;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;

class Payment extends Data
{
    public string $title;
    public string $uuid;
    public PaymentType $type;
    public BankTransferDetails|CashOnDeliveryDetails|CreditCardDetails $details;
    public CarbonImmutable $updated_at;
    public CarbonImmutable $created_at;
}
