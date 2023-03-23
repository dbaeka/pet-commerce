<?php

namespace App\Dtos;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, mixed>
 */
abstract class BaseDto implements Arrayable
{
    final public function toArray(): array
    {
        return get_object_vars($this);
    }
}
