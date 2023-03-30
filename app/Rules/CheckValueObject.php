<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use Throwable;

class CheckValueObject implements ValidationRule, DataAwareRule
{
    /** @var array<string, mixed> */
    private array $data;

    public function __construct(
        protected string   $value_class = '',
        protected ?Closure $factory = null
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
            if ($this->factory) {
                $factory = $this->factory;
                $factory($this->data, $value);
            } else {
                $this->value_class::{'from'}($value);
            }
        } catch (Throwable) {
            $fail('The :attribute must have the right fields');
        }
    }

    /**
     * @param array<string, mixed> $data
     * @return $this
     */
    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }
}
