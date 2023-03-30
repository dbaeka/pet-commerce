<?php

namespace App\Http\Requests\v1\Payment;

use App\DataObjects\PaymentType\PaymentTypeDetailsFactory;
use App\Enums\PaymentType;
use App\Rules\CheckValueObject;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

/**
 * @OA\Schema(
 *    schema="UpdatePaymentRequest",
 *    ref="#/components/schemas/StorePaymentRequest"
 * )
 */
class UpdatePaymentRequest extends FormRequest
{
    /**
     * @param Authenticatable $user
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize(Authenticatable $user): bool
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
                    factory: function (array $attributes, mixed $value) {
                        $type = PaymentType::from($attributes['type']);
                        return PaymentTypeDetailsFactory::make($type, $value);
                    }
                )
            ]
        ];
    }
}
