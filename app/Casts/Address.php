<?php

namespace App\Casts;

use App\Values\Address as AddressVO;

/**
 * @extends SimpleValueCast<AddressVO>
 */
class Address extends SimpleValueCast
{
    protected string $value_class = AddressVO::class;
}
