<?php

namespace SocialDept\Schema\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use SocialDept\Schema\Parser\Nsid as NsidParser;

class Nsid implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail("The {$attribute} must be a string.");

            return;
        }

        try {
            NsidParser::parse($value);
        } catch (\Exception) {
            $fail("The {$attribute} is not a valid NSID.");
        }
    }
}
