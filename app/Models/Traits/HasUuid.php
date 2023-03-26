<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
    protected static function bootHasUuid(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::orderedUuid()->toString();
            }
        });
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    final public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
