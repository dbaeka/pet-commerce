<?php

namespace App\Http\Requests\v1\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * @OA\Schema(
 *    schema="ResetPasswordTokenRequest",
 *    required={"token","email","password","password_confirmation"},
 *    @OA\Property(
 *     property="token",
 *     type="string",
 *     description="User reset token",
 *    ),
 *     @OA\Property(
 *     property="email",
 *     type="string",
 *     description="User email",
 *    ),
 *    @OA\Property(
 *     property="password",
 *     type="string",
 *     format="password",
 *     description="User password",
 *    ),
 *     @OA\Property(
 *     property="password_confirmation",
 *     type="string",
 *     format="password",
 *     description="User password",
 *    ),
 * )
 */
class ResetPasswordTokenRequest extends FormRequest
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
            "token" => ['required', 'string'],
            "email" => ['required', 'email'],
            "password" => ['required', Password::defaults(), 'confirmed'],
            "password_confirmation" => ['required'],
        ];
    }
}
