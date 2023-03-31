<?php

namespace App\DataObjects;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Optional;

class Order extends Data
{
    public string|Optional $user_uuid;
    public string|Optional $order_status_uuid;
    public string|Optional $payment_uuid;
    public string|Optional $uuid;
    public float $amount;
    /** @var DataCollection<int, ProductItem>  */
    public DataCollection $products;
    public Address $address;
    public User|Optional $user;
    public OrderStatus|Optional $order_status;
    public Payment|Optional $payment;
    public float|Optional $delivery_fee = 0;
    public CarbonImmutable|Optional $updated_at;
    public CarbonImmutable|Optional $created_at;
    public CarbonImmutable|Optional|null $shipped_at;
}
