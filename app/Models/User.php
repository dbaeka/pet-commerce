<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasUuid;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'address',
        'is_admin',
        'phone_number'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint
     */
    protected $hidden = [
        'password', 'id', 'is_admin'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_marketing' => 'boolean',
        'is_admin' => 'boolean',
    ];

    /**
     * Get the current order status.
     * @return HasMany<Order>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the current order status.
     * @return HasMany<JwtToken>
     */
    public function jwt_tokens(): HasMany
    {
        return $this->hasMany(JwtToken::class, 'user_uuid', 'uuid');
    }
}
