<?php

namespace App\Dtos;

use App\Casts\Products;
use App\Values\Address;
use Illuminate\Support\Collection;

class Order extends BaseDto
{
    protected array $casts = [
        'address' => \App\Casts\Address::class,
        'products' => Products::class
    ];


    public string $user_uuid = '';
    public string $order_status_uuid = '';
    public string $payment_uuid = '';
    public string $uuid = '';
    public float $amount = 0;
    /** @var Collection<int, \App\Values\Product>|null $products */
    public ?Collection $products = null;
    public ?Address $address = null;
    public float $delivery_fee = 0;
    public string $updated_at = '';
    public string $created_at = '';
    public string $shipped_at = '';
}
