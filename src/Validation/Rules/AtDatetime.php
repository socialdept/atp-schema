<?php

namespace SocialDept\AtpSchema\Validation\Rules;

use Closure;
use DateTime;
use Illuminate\Contracts\Validation\ValidationRule;

class AtDatetime implements ValidationRule
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

        if (! $this->isValidAtDatetime($value)) {
            $fail("The {$attribute} is not a valid AT Protocol datetime.");
        }
    }

    /**
     * Validate AT Protocol datetime format.
     *
     * Must be ISO 8601 format with timezone (typically UTC)
     * Example: 2024-01-01T00:00:00Z or 2024-01-01T00:00:00.000Z
     */
    protected function isValidAtDatetime(string $value): bool
    {
        // Try to parse as DateTime with ISO 8601 format
        $datetime = DateTime::createFromFormat(DateTime::ATOM, $value);

        if ($datetime !== false) {
            return true;
        }

        // Also try with milliseconds
        $datetime = DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $value);

        if ($datetime !== false) {
            return true;
        }

        // Try standard ISO 8601 with Z
        $datetime = DateTime::createFromFormat('Y-m-d\TH:i:s\Z', $value);

        return $datetime !== false;
    }
}
