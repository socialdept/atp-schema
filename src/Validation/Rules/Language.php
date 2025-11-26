<?php

namespace SocialDept\AtpSchema\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Language implements ValidationRule
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

        if (! $this->isValidBcp47($value)) {
            $fail("The {$attribute} is not a valid BCP 47 language code.");
        }
    }

    /**
     * Validate BCP 47 language code.
     *
     * Format: language[-script][-region][-variant]
     * Examples: en, en-US, zh-Hans, en-GB-oed
     */
    protected function isValidBcp47(string $value): bool
    {
        // BCP 47 regex pattern
        // Primary language: 2-3 letter code or 4-8 letter code
        // Script: 4 letters (optional)
        // Region: 2 letters or 3 digits (optional)
        // Variant: 5-8 alphanumeric or digit followed by 3 alphanumeric (optional, repeatable)
        $pattern = '/^
            ([a-z]{2,3}|[a-z]{4}|[a-z]{5,8})              # Primary language
            (-[A-Z][a-z]{3})?                              # Script (optional)
            (-([A-Z]{2}|[0-9]{3}))?                        # Region (optional)
            (-([a-z0-9]{5,8}|[0-9][a-z0-9]{3}))*          # Variant (optional, repeatable)
            (-[a-z]-[a-z0-9]{2,8})*                        # Extension (optional)
            (-x-[a-z0-9]{1,8})?                            # Private use (optional)
        $/xi';

        if (! preg_match($pattern, $value)) {
            return false;
        }

        // Additional validation: Check if primary language is valid
        $parts = explode('-', $value);
        $primaryLanguage = strtolower($parts[0]);

        // Language code should be 2-3 characters (ISO 639-1 or 639-2)
        $length = strlen($primaryLanguage);

        return $length >= 2 && $length <= 8;
    }
}
