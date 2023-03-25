<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class QueryRequest extends FormRequest
{
    final public function validateResolved(): void
    {
        /** @var array<string, mixed> $query_params */
        $query_params = $this->query();
        $this->attributes->add($query_params);
        parent::validateResolved();
    }
}
