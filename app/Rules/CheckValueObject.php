<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use Throwable;

class CheckValueObject implements ValidationRule
{
    public function __construct(
        protected string $value_class
    ) {
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
        try {
            new $this->value_class(...$value);
        } catch (Throwable) {
            $fail('The :attribute must have the right fields');
        }
    }
}
