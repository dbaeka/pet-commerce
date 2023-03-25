<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;

class UserCreateResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var mixed $resource */
        $resource = $this->resource;
        return $this->addDefaultValues([
            "data" => [
                "uuid" => $resource->uuid,
                "first_name" => $resource->first_name,
                "last_name" => $resource->last_name,
                "email" => $resource->email,
                "address" => $resource->address,
                "phone_number" => $resource->phone_number,
                "updated_at" => $resource->updated_at,
                "created_at" => $resource->created_at,
                "token" => $resource->token
            ],
        ]);
    }
}
