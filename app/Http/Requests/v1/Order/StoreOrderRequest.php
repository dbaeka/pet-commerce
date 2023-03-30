<?php

namespace App\Http\Requests\v1\Order;

use App\DataObjects\Address;
use App\DataObjects\ProductItem;
use App\Rules\CheckValueObject;
use App\Rules\ExistsUuidsList;
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
            'products' => ['required', 'array', new ExistsUuidsList('products')],
            'products.*' => ['required', new CheckValueObject(ProductItem::class)],
            'address' => ['required', new CheckValueObject(Address::class)],
        ];
    }
}
