<?php

namespace App\Http\Requests\v1\Admin;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *    schema="AdminUserEditRequest",
 *    ref="#/components/schemas/AdminUserCreateRequest"
 * )
 */
class AdminUserEditRequest extends FormRequest
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
            "first_name" => ['required'],
            "last_name" => ['required'],
            "email" => ['required', 'email'],
            "password" => ['required', 'confirmed'],
            "password_confirmation" => ['required'],
            "address" => ['required'],
            "phone_number" => ['required'],
            'is_marketing' => ['boolean', 'nullable'],
            'avatar' => ['string']
        ];
    }
}
