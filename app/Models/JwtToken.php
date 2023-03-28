<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\JwtToken
 *
 * @property int $id
 * @property string $user_uuid
 * @property string $unique_id
 * @property string $token_title
 * @property array|null $restrictions
 * @property array|null $permissions
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $last_used_at
 * @property \Illuminate\Support\Carbon|null $refreshed_at
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\JwtTokenFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|JwtToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JwtToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JwtToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|JwtToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JwtToken whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JwtToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JwtToken whereLastUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JwtToken wherePermissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JwtToken whereRefreshedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JwtToken whereRestrictions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JwtToken whereTokenTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JwtToken whereUniqueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JwtToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JwtToken whereUserUuid($value)
 * @mixin \Eloquent
 */
class JwtToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_uuid',
        'unique_id',
        'token_title',
        'restrictions',
        'permissions',
        'expires_at'
    ];

    protected $casts = [
        'restrictions' => 'array',
        'permissions' => 'array',
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
        'refreshed_at' => 'datetime',
    ];

    protected $hidden = ['id'];



    /**
     * Get the user owning the jwt token.
     * @return BelongsTo<User, JwtToken>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }
}
