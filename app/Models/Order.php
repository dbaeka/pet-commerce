<?php

namespace App\Models;

use App\Casts\Address;
use App\Casts\Products;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property string $user_uuid
 * @property string $order_status_uuid
 * @property string $payment_uuid
 * @property string $uuid
 * @property \Illuminate\Support\Collection $products
 * @property \App\Casts\Address|object $address
 * @property float|null $delivery_fee
 * @property float $amount
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $shipped_at
 * @property-read \App\Models\OrderStatus|null $order_status
 * @property-read \App\Models\Payment|null $payment
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\OrderFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDeliveryFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOrderStatusUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePaymentUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereProducts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShippedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUserUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUuid($value)
 * @mixin \Eloquent
 */
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
