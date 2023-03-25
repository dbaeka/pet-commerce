<?php

namespace App\Http\Requests\v1;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

class AdminUserListingRequest extends DefaultPaginationRequest
{
    protected string $table_name = 'users';

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
    public function additionalRules(): array
    {
        return [
            'email' => ['email'],
            'first_name' => ['string'],
            'phone' => ['string'],
            'address' => ['string'],
            'created_at' => ['date', 'date_format:Y-m-d'],
            'marketing' => ['boolean']
        ];
    }
}
