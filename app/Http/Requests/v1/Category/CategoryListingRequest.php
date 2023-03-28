<?php

namespace App\Http\Requests\v1\Category;

use App\Http\Requests\v1\DefaultPaginationRequest;

class CategoryListingRequest extends DefaultPaginationRequest
{
    protected string $table_name = 'categories';
}
