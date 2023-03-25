<?php

namespace App\Models;

use App\Casts\PaymentDetails;
use App\Enums\PaymentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $casts = [
        'type' => PaymentType::class,
        'details' => PaymentDetails::class
    ];

    protected $hidden = ['id'];
}
