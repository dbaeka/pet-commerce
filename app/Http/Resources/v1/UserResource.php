<?php

namespace App\Http\Resources\v1;

use App\DataObjects\User;
use Illuminate\Http\Request;

class UserResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var User $resource */
        $resource = $this->resource;
        return $this->addDefaultValues([
            "data" => [
                "uuid" => $resource->uuid,
                "first_name" => $resource->first_name,
                "last_name" => $resource->last_name,
                "email" => $resource->email,
                "address" => $resource->address,
                "phone_number" => $resource->phone_number,
                "email_verified_at" => $resource->email_verified_at,
                "avatar" => $resource->avatar,
                "is_marketing" => $resource->is_marketing,
                "updated_at" => $resource->updated_at,
                "created_at" => $resource->created_at,
                "last_login_at" => $resource->last_login_at
            ],
        ]);
    }
}
