<?php

namespace App\Http\Requests\v1;

class OrderListingRequest extends DefaultPaginationRequest
{
    protected string $table_name = 'orders';

    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }
}
