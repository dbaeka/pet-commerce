<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * @template T
 *
 * @implements CastsAttributes<T, T>
 */
abstract class SimpleValueCast implements CastsAttributes
{
    protected string $value_class;

    /**
     * Cast the given value.
     *
     * @param array<string, mixed> $attributes
     * @return T|object
     */
    public function get(?Model $model, string $key, mixed $value, array $attributes)
    {
        $value = json_decode($value, true);
        return new $this->value_class(...$value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model|null $model
     * @param string $key
     * @param mixed|object $value
     * @param array<string, mixed> $attributes
     * @return false|string
     */
    public function set(?Model $model, string $key, mixed $value, array $attributes): false|string
    {
        if (!is_a($value, $this->value_class)) {
            throw new InvalidArgumentException("The given value is not an {$this->value_class} instance.");
        }
        return json_encode($value);
    }
}
