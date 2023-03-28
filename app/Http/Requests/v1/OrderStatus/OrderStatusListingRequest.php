<?php

namespace App\Http\Requests\v1\OrderStatus;

use App\Http\Requests\v1\DefaultPaginationRequest;

class OrderStatusListingRequest extends DefaultPaginationRequest
{
    protected string $table_name = 'order_statuses';
}
