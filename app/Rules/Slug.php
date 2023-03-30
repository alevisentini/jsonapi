<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Slug implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (strpos($value, '_') !== false) {
            $fail('The slug cannot contain an underscore.');
        }

        if (strpos($value, '-') === 0) {
            $fail('The slug cannot start with a dash.');
        }

        if (strrpos($value, '-') === strlen($value) - 1) {
            $fail('The slug cannot end with a dash.');
        }
    }
}
