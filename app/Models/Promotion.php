<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Promotion
 *
 * @property int $id
 * @property string $uuid
 * @property string $title
 * @property string $content
 * @property array $metadata
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Database\Factories\PromotionFactory factory($count = null, $state = [])
 * @method static Builder|Promotion invalid()
 * @method static Builder|Promotion newModelQuery()
 * @method static Builder|Promotion newQuery()
 * @method static Builder|Promotion query()
 * @method static Builder|Promotion valid()
 * @method static Builder|Promotion whereContent($value)
 * @method static Builder|Promotion whereCreatedAt($value)
 * @method static Builder|Promotion whereId($value)
 * @method static Builder|Promotion whereMetadata($value)
 * @method static Builder|Promotion whereTitle($value)
 * @method static Builder|Promotion whereUpdatedAt($value)
 * @method static Builder|Promotion whereUuid($value)
 * @mixin \Eloquent
 */
class Promotion extends Model
{
    use HasFactory;
    use HasUuid;

    protected $casts = [
        'metadata' => 'array'
    ];

    protected $hidden = ['id', 'valid_to'];

    /**
     * Scope a query to only include valid promotions.
     *
     * @param Builder<Promotion> $query
     */
    public function scopeValid(Builder $query): void
    {
        $query->where('valid_to', '>=', now());
    }


    /**
     * Scope a query to only include invalid promotions.
     *
     * @param Builder<Promotion> $query
     */
    public function scopeInvalid(Builder $query): void
    {
        $query->where('valid_to', '<', now());
    }
}
