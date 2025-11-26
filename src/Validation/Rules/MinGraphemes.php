<?php

namespace SocialDept\AtpSchema\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MinGraphemes implements ValidationRule
{
    /**
     * Minimum grapheme count.
     */
    protected int $min;

    /**
     * Create a new rule instance.
     */
    public function __construct(int $min)
    {
        $this->min = $min;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail("The {$attribute} must be a string.");

            return;
        }

        $count = grapheme_strlen($value);

        if ($count < $this->min) {
            $fail("The {$attribute} must be at least {$this->min} graphemes.");
        }
    }
}
