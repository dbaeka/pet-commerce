<?php

namespace App\Models;

use App\Casts\Address;
use App\Casts\Products;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $casts = [
        'address' => Address::class,
        'products' => Products::class,
        'shipped_at' => 'datetime',
    ];

    protected $hidden = ['id', 'user_uuid', 'payment_uuid', 'order_status_uuid'];

    /**
     * Get the current order status.
     * @return HasOne<OrderStatus>`
     */
    public function order_status(): HasOne
    {
        return $this->hasOne(OrderStatus::class, 'uuid', 'order_status_uuid');
    }

    /**
     * Get the user owning the order.
     * @return BelongsTo<User, Order>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    /**
     * Get the order payment.
     * @return HasOne<Payment>
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'uuid', 'payment_uuid');
    }
}
