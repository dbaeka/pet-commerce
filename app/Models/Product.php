<?php

namespace App\Models;

use App\Casts\ProductMetadata;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class Product extends Model
{
    use HasFactory;
    use HasUuid;
    use HasJsonRelationships;

    protected $with = ['category', 'brand'];

    protected $fillable = ['title', 'category_uuid', 'price', 'description', 'metadata'];

    protected $casts = [
        'metadata' => ProductMetadata::class,
    ];

    protected $hidden = ['id'];


    /**
     * Get the category that has the product.
     * @return BelongsTo<Category, Product>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_uuid', 'uuid');
    }

    /**
     * @return HasOne
     */
    public function brand(): HasOne
    {
        return $this->hasOne(Brand::class, 'uuid', 'metadata->brand');
    }
}
