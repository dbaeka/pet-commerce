<?php

namespace App\Http\Requests\v1\User;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * @OA\Schema(
 *    schema="UserEditRequest",
 *    ref="#/components/schemas/UserCreateRequest"
 * )
 */
class UserEditRequest extends FormRequest
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
        return !$user_model->is_admin;
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
            "password" => ['required', Password::defaults(), 'confirmed'],
            "password_confirmation" => ['required'],
            "address" => ['required'],
            "phone_number" => ['required'],
            'is_marketing' => ['boolean', 'nullable']
        ];
    }
}
