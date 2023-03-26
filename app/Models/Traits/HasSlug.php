<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->{self::slugKey()});
            }
        });

        static::updating(function ($model) {
            $new_slug = Str::slug($model->{self::slugKey()});
            if (strcmp($new_slug, $model->slug) != 0) {
                $model->slug = $new_slug;
            }
        });
    }

    protected static function slugKey(): string
    {
        return 'title';
    }
}
