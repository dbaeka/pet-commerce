<?php

namespace App\Http\Requests\v1\Product;

use App\DataObjects\ProductMetadata;
use App\Models\User;
use App\Rules\CheckValueObject;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *    schema="StoreProductRequest",
 *    required={"title", "category_uuid", "price", "description", "metadata"},
 *    @OA\Property(
 *     property="title",
 *     type="string",
 *     description="Product title",
 *    ),
 *     @OA\Property(
 *     property="category_uuid",
 *     type="string",
 *     description="Category uuid",
 *    ),
 *     @OA\Property(
 *     property="price",
 *     type="number",
 *     description="Product price",
 *    ),
 *     @OA\Property(
 *     property="description",
 *     type="string",
 *     description="Product description",
 *    ),
 *     @OA\Property(
 *     property="metadata",
 *     ref="#/components/schemas/ProductMetadata"
 *    ),
 * )
 */
class StoreProductRequest extends FormRequest
{
    /**
     * @param Authenticatable $user
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize(Authenticatable $user): bool
    {
        /** @var User $user_model */
        $user_model = $user;
        return $user_model->is_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'category_uuid' => ['required', 'exists:categories,uuid'],
            'price' => ['required', 'numeric'],
            'description' => ['required', 'string'],
            'metadata' => ['required', new CheckValueObject(ProductMetadata::class)]
        ];
    }
}
