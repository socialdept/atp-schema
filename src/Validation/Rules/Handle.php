<?php

namespace SocialDept\Schema\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Handle implements ValidationRule
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

        // Check if Resolver package is available
        if (class_exists('SocialDept\Resolver\Support\Identity')) {
            if (! \SocialDept\Resolver\Support\Identity::isHandle($value)) {
                $fail("The {$attribute} is not a valid handle.");
            }

            return;
        }

        // Fallback validation if Resolver is not available
        if (! $this->isValidHandle($value)) {
            $fail("The {$attribute} is not a valid handle.");
        }
    }

    /**
     * Fallback handle validation.
     */
    protected function isValidHandle(string $value): bool
    {
        // Handle format: domain.tld (DNS name)
        // Must be at least 3 chars, no spaces, valid DNS characters
        if (strlen($value) < 3 || strlen($value) > 253) {
            return false;
        }

        // Check for valid DNS hostname format
        return (bool) preg_match('/^[a-zA-Z0-9]([a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(\.[a-zA-Z0-9]([a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/', $value);
    }
}
