<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UrlFilter implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //filter onion only
        if (!preg_match('/^http?:\/\/[a-z2-7]{16,56}\.onion(\/.*)?$/', $value)) {
            $fail('Invalid onion link');
        }

    }
}
