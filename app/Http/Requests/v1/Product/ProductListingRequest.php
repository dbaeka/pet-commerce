<?php

namespace App\Http\Requests\v1\Product;

use App\Http\Requests\v1\DefaultPaginationRequest;

class ProductListingRequest extends DefaultPaginationRequest
{
    protected string $table_name = 'products';

    /**
     * @return bool
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
    public function additionalRules(): array
    {
        return [
            'category_uuid' => ['string',],
            'price' => ['numeric'],
            'brand_uuid' => ['string',],
            'title' => ['string'],
            'uuid' => ['string']
        ];
    }

}
