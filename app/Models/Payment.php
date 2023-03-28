<?php

namespace App\Models;

use App\Casts\PaymentDetails;
use App\Enums\PaymentType;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * App\Models\Payment
 *
 * @property int $id
 * @property string $uuid
 * @property PaymentType $type
 * @property \App\Values\PaymentType\PaymentTypeDetails $details
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\PaymentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUuid($value)
 * @mixin \Eloquent
 */
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
