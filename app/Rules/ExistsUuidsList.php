<?php

namespace App\Rules;

use Closure;
use DB;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ExistsUuidsList implements ValidationRule
{
    private string $table_name;

    public function __construct(string $table_name)
    {
        $this->table_name = $table_name;
    }

    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_array($value)) {
            $uuids = collect($value)->pluck('uuid');
            $model_items = DB::table($this->table_name)->whereIn('uuid', $uuids);
            if ($uuids->count() == $model_items->count()) {
                return;
            }
        }
        $fail(":attribute has invalid {$this->table_name} uuid provided");
    }
}
