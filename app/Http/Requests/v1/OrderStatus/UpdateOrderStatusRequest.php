<?php

namespace App\Http\Requests\v1\OrderStatus;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *    schema="UpdateOrderStatusRequest",
 *    ref="#/components/schemas/StoreOrderStatusRequest"
 * )
 */
class UpdateOrderStatusRequest extends FormRequest
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
            'title' => ['required', 'string']
        ];
    }
}
