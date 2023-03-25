<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Response(response="OK", description="OK"),
 * @OA\Response(response="NoContent", description="No Content"),
 * @OA\Response(response="Unauthorized", description="Unauthorized"),
 * @OA\Response(response="NotFound", description="Page not found"),
 * @OA\Response(response="Unprocessable", description="Unprocessable Entity"),
 * @OA\Response(response="ServerError", description="Internal server error")
 */
class BaseResource extends JsonResource
{
    /**
     * @param array<string, mixed> $values
     * @return array<string, mixed>
     */
    protected function addDefaultValues(array $values): array
    {
        return array_merge([
            "success" => 1,
            "error" => null,
            "errors" => [],
        ], $values);
    }
}
