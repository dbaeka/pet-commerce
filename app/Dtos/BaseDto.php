<?php

namespace App\Dtos;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, mixed>
 */
abstract class BaseDto implements Arrayable
{
    final private function __construct()
    {
    }

    /**
     * @param array<string, mixed> $attributes
     * @return static
     */
    final public static function make(array $attributes = []): static
    {
        $obj = new static();
        foreach ($attributes as $key => $value) {
            if (property_exists(static::class, $key)) {
                $obj->$key = $value;
            }
        }
        return $obj;
    }

    final public function toArray(): array
    {
        return get_object_vars($this);
    }
}
