<?php

namespace SocialDept\Schema\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MaxGraphemes implements ValidationRule
{
    /**
     * Maximum grapheme count.
     */
    protected int $max;

    /**
     * Create a new rule instance.
     */
    public function __construct(int $max)
    {
        $this->max = $max;
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

        if ($count > $this->max) {
            $fail("The {$attribute} may not be greater than {$this->max} graphemes.");
        }
    }
}
