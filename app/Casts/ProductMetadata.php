<?php

namespace App\Casts;

use App\Values\ProductMetadata as Metadata;

/**
 * @extends SimpleValueCast<Metadata>
 */
class ProductMetadata extends SimpleValueCast
{
    protected string $value_class = Metadata::class;
}
