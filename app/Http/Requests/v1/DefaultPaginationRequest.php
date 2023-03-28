<?php

namespace App\Http\Requests\v1;

use App\Http\Requests\QueryRequest;
use Schema;

abstract class DefaultPaginationRequest extends QueryRequest
{
    protected string $table_name;

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
    final public function rules(): array
    {
        return array_merge([
            'page' => ['numeric', 'gt:0'],
            'limit' => ['numeric', 'gt:0'],
            'sort_by' => ['string', 'in:' . implode(',', $this->getSortByCols())],
            'desc' => ['boolean']
        ], $this->additionalRules());
    }

    /**
     * @return array<int, string>
     */
    private function getSortByCols(): array
    {
        return Schema::getColumnListing($this->table_name);
    }

    /**
     * @return array<string, mixed>
     */
    public function additionalRules(): array
    {
        return [];
    }
}
