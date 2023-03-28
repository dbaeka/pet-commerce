<?php

namespace App\Dtos;

use App\Enums\PaymentType;
use App\Values\PaymentType\PaymentTypeDetails;

class Payment extends BaseDto
{
    public string $title = '';
    public string $uuid = '';
    public ?PaymentType $type = null;
    public ?PaymentTypeDetails $details = null;
    public string $updated_at = '';
    public string $created_at = '';
}
