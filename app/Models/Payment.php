<?php

namespace App\Models;

use App\Casts\PaymentDetails;
use App\Enums\PaymentType;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Payment extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = ['type', 'details'];

    protected $casts = [
        'type' => PaymentType::class,
        'details' => PaymentDetails::class
    ];

    protected $hidden = ['id'];

    /**
     * Get the user owning the order.
     * @return HasOne<Order>
     */
    public function order(): HasOne
    {
        return $this->hasOne(Order::class, 'payment_uuid', 'uuid');
    }

    /**
     * Get the user owning the order.
     * @return HasOneThrough<User>
     */
    public function user(): HasOneThrough
    {
        return $this->hasOneThrough(
            User::class,
            Order::class,
            'payment_uuid',
            'uuid',
            'uuid',
            'user_uuid'
        );
    }
}
