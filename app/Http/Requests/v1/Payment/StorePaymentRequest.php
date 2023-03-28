<?php

namespace App\Http\Requests\v1\Payment;

use App\Enums\PaymentType;
use App\Rules\CheckValueObject;
use App\Values\PaymentType\PaymentTypeDetails;
use App\Values\PaymentType\PaymentTypeDetailsFactory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

/**
 * @OA\Schema(
 *    schema="StorePaymentRequest",
 *    required={"type", "details"},
 *    @OA\Property(
 *     property="type",
 *     type="string",
 *     description="Payment type",
 *     enum={"credit_card", "cash_on_delivery", "bank_transfer"}
 *    ),
 *    @OA\Property(
 *     property="details",
 *     type="object",
 *     description="Payment details",
 *     oneOf={
 *       @OA\Schema(ref="#/components/schemas/BankTransferDetails"),
 *       @OA\Schema(ref="#/components/schemas/CreditCardDetails"),
 *       @OA\Schema(ref="#/components/schemas/CashOnDeliveryDetails"),
 *     }
 *    ),
 * )
 */
class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', new Enum(PaymentType::class)],
            'details' => [
                'required',
                new CheckValueObject(
                    PaymentTypeDetails::class,
                    function (array $attributes, mixed $value) {
                        $type = PaymentType::from($attributes['type']);
                        return PaymentTypeDetailsFactory::make($type, $value);
                    }
                )
            ]
        ];
    }
}
