<?php

namespace App\DataObjects;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class Order extends Data
{
    public string $user_uuid;
    public string $order_status_uuid;
    public string $payment_uuid;
    public string $uuid;
    public float $amount;
    /** @var DataCollection<int, ProductItem>  */
    public DataCollection $products;
    public Address $address;
    public float $delivery_fee;
    public CarbonImmutable $updated_at;
    public CarbonImmutable $created_at;
    public ?CarbonImmutable $shipped_at;
}
