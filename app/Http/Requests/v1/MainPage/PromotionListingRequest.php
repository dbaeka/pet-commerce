<?php

namespace App\Http\Requests\v1\MainPage;

use App\Http\Requests\v1\DefaultPaginationRequest;

class PromotionListingRequest extends DefaultPaginationRequest
{
    protected string $table_name = 'promotions';

    /**
     *
     * @return array<string, mixed>
     */
    public function additionalRules(): array
    {
        return [
            'valid' => ['boolean'],
        ];
    }
}
