<?php

namespace App\Http\Requests\v1\Order;

use App\Rules\CheckValueObject;
use App\Values\Address;
use App\Values\Product;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *    schema="StoreOrderRequest",
 *    required={"order_status_uuid", "payment_uuid", "products", "address"},
 *    @OA\Property(
 *     property="order_status_uuid",
 *     type="string",
 *     description="Order status uuid",
 *    ),
 *    @OA\Property(
 *     property="payment_uuid",
 *     type="string",
 *     description="Payment uuid",
 *    ),
 *    @OA\Property(
 *     property="products",
 *     type="array",
 *     description="Order products",
 *     items=@OA\Items(
 *       ref="#/components/schemas/ProductValueRequest"
 *     )
 *    ),
 *    @OA\Property(
 *     property="address",
 *     ref="#/components/schemas/Address"
 *    ),
 * )
 */
class StoreOrderRequest extends FormRequest
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
            'order_status_uuid' => ['required', 'exists:order_statuses,uuid'],
            'payment_uuid' => ['required', 'exists:payments,uuid'],
            'products' => [
                'required',
                'array',
                function (string $attribute, array $value, Closure $fail) {
                    $product_uuids = collect($value)->pluck('uuid');
                    $in_products = \App\Models\Product::query()->whereIn('uuid', $product_uuids);
                    if ($product_uuids->count() != $in_products->count()) {
                        $fail(':attribute has invalid product uuid provided');
                    }
                }
            ],
            'products.*' => [
                'required', new CheckValueObject(Product::class),],
            'address' => ['required', new CheckValueObject(Address::class)],
        ];
    }
}
