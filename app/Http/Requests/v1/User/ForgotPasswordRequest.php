<?php

namespace App\Http\Requests\v1\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *    schema="ForgotPasswordRequest",
 *    required={"email"},
 *     @OA\Property(
 *     property="email",
 *     type="string",
 *     description="User email",
 *    ),
 * )
 */
class ForgotPasswordRequest extends FormRequest
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
            "email" => ['required', 'email'],
        ];
    }
}
