<?php

namespace App\Http\Requests\v1\Payment;

use App\Http\Requests\v1\DefaultPaginationRequest;

class PaymentListingRequest extends DefaultPaginationRequest
{
    protected string $table_name = 'payments';
}
