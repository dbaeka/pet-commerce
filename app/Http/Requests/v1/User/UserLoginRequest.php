<?php

namespace App\Http\Requests\v1\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *    schema="UserLoginRequest",
 *    required={"email","password"},
 *    @OA\Property(
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
 * )
 */
class UserLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
            'email' => ['required', 'email'],
            'password' => ['required']
        ];
    }
}
