<?php

namespace App\Http\Requests\v1\Order;

use App\Http\Requests\v1\DefaultPaginationRequest;

class DashboardListingRequest extends DefaultPaginationRequest
{
    protected string $table_name = 'orders';

    public function additionalRules(): array
    {
        return [
            'fixed_range' => ['string', 'in:today,monthly,yearly'],
            'date_range' => ['array:to,from'],
            'date_range.from' => ['date_format:Y-m-d'],
            'date_range.to' => ['date_format:Y-m-d']
        ];
    }
}
