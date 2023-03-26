<?php

namespace App\Http\Requests\v1\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *    schema="UserCreateRequest",
 *    ref="#/components/schemas/AdminUserCreateRequest"
 * )
 */
class UserCreateRequest extends FormRequest
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
            "first_name" => ['required'],
            "last_name" => ['required'],
            "email" => ['required', 'email'],
            "password" => ['required', 'confirmed'],
            "password_confirmation" => ['required'],
            "address" => ['required'],
            "phone_number" => ['required'],
            'is_marketing' => ['boolean', 'nullable']
        ];
    }
}
