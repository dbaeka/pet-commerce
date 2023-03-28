<?php

namespace App\Http\Requests\v1\Order;

use App\Http\Requests\v1\DefaultPaginationRequest;

class OrderListingRequest extends DefaultPaginationRequest
{
    protected string $table_name = 'orders';
}
