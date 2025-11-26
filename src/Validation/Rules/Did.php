<?php

namespace SocialDept\AtpSchema\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Did implements ValidationRule
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
        if (class_exists('SocialDept\AtpResolver\Support\Identity')) {
            if (! \SocialDept\AtpResolver\Support\Identity::isDid($value)) {
                $fail("The {$attribute} is not a valid DID.");
            }

            return;
        }

        // Fallback validation if Resolver is not available
        if (! $this->isValidDid($value)) {
            $fail("The {$attribute} is not a valid DID.");
        }
    }

    /**
     * Fallback DID validation.
     */
    protected function isValidDid(string $value): bool
    {
        // DID format: did:method:method-specific-id
        return (bool) preg_match('/^did:[a-z]+:[a-zA-Z0-9._:%-]+$/', $value);
    }
}
