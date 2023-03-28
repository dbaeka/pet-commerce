<?php

namespace App\Http\Requests\v1\Brand;

use App\Http\Requests\v1\DefaultPaginationRequest;

class BrandListingRequest extends DefaultPaginationRequest
{
    protected string $table_name = 'brands';
}
