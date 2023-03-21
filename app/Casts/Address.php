<?php

namespace App\Casts;

use App\Values\Address as AddressVO;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * @implements CastsAttributes<AddressVO, AddressVO>
 */
class Address implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param array<string, mixed> $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): AddressVO
    {
        return new AddressVO(
            $value['shipping'],
            $value['billing']
        );
    }

    /**
     * Prepare the given value for storage.
     *
     * @param array<string, mixed> $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): false|string
    {
        if (!$value instanceof AddressVO) {
            throw new InvalidArgumentException('The given value is not an Address instance.');
        }
        return json_encode($value);
    }
}
