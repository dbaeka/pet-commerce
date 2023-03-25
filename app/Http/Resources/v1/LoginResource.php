<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;

class LoginResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->addDefaultValues([
            "data" => [
                "token" => $this->resource,
            ],
        ]);
    }
}
