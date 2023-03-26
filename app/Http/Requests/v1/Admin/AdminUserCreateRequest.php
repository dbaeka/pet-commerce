<?php

namespace App\Http\Requests\v1\Admin;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *    schema="AdminUserCreateRequest",
 *    required={"first_name","last_name","email","password","password_confirmation","address","phone_number"},
 *    @OA\Property(
 *     property="first_name",
 *     type="string",
 *     description="User firstname",
 *    ),
 *     @OA\Property(
 *     property="last_name",
 *     type="string",
 *     description="User lastname",
 *    ),
 *     @OA\Property(
 *     property="email",
 *     type="string",
 *     description="User email",
 *    ),
 *    @OA\Property(
 *     property="password",
 *     type="string",
 *     description="User password",
 *    ),
 *     @OA\Property(
 *     property="password_confirmation",
 *     type="string",
 *     description="User password",
 *    ),
 *     @OA\Property(
 *     property="avatar",
 *     type="string",
 *     description="Avatar image uuid",
 *    ),
 *     @OA\Property(
 *     property="address",
 *     type="string",
 *     description="User main address",
 *    ),
 *     @OA\Property(
 *     property="phone_number",
 *     type="string",
 *     description="User main phone number",
 *    ),
 *     @OA\Property(
 *     property="is_marketing",
 *     type="boolean",
 *     description="User marketing preferences",
 *    ),
 * )
 */
class AdminUserCreateRequest extends FormRequest
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
            'is_marketing' => ['boolean', 'nullable']
        ];
    }
}
