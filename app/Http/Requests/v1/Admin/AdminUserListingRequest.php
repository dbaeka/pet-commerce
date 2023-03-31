<?php

namespace App\Http\Requests\v1\Admin;

use App\Http\Requests\v1\DefaultPaginationRequest;
use App\Models\User;
use Auth;

class AdminUserListingRequest extends DefaultPaginationRequest
{
    protected string $table_name = 'users';

    /**
     * @return bool
     */
    public function authorize(): bool
    {
        /** @var User $user */
        $user = Auth::user();
        return $user->is_admin;
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
            'is_marketing' => ['in:true,false']
        ];
    }
}
