<?php

namespace App\DataObjects;

use App\DataObjects\Casts\PaymentDetails;
use App\DataObjects\PaymentType\BasePaymentDetails;
use App\DataObjects\PaymentType\PaymentTypeDetailsFactory;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class Payment extends Data
{
    public string|Optional $uuid;
    public PaymentType $type;
    #[WithCast(PaymentDetails::class)]
    public BasePaymentDetails $details;
    public string|Optional|null $gateway;
    public PaymentStatus $status;
    public CarbonImmutable|Optional $updated_at;
    public CarbonImmutable|Optional $created_at;

    /**
     * @param array<string, mixed> $data
     */
    public static function fromModel(array $data): Payment
    {
        $details = PaymentTypeDetailsFactory::make($data['type'], $data['details']);
        $object = new self();
        $object->uuid = Optional::create();
        $object->status = PaymentStatus::from($data['status']);
        $object->gateway = Optional::create();
        $object->updated_at = Optional::create();
        $object->created_at = Optional::create();
        $object->type = PaymentType::from($data['type']);
        $object->details = $details;
        return $object;
    }
}
