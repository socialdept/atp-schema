<?php

namespace SocialDept\Schema\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AtUri implements ValidationRule
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

        if (! $this->isValidAtUri($value)) {
            $fail("The {$attribute} is not a valid AT URI.");
        }
    }

    /**
     * Validate AT URI format.
     *
     * Format: at://did:plc:xyz/collection/rkey
     * or: at://handle.domain/collection/rkey
     */
    protected function isValidAtUri(string $value): bool
    {
        // Must start with at://
        if (! str_starts_with($value, 'at://')) {
            return false;
        }

        // Remove at:// prefix
        $remainder = substr($value, 5);

        // Must have at least authority part
        if (empty($remainder)) {
            return false;
        }

        // Split into authority and path
        $parts = explode('/', $remainder, 2);
        $authority = $parts[0];

        // Validate authority (DID or handle)
        $didRule = new Did;
        $handleRule = new Handle;

        $isValidDid = true;
        $isValidHandle = true;

        $didRule->validate('authority', $authority, function () use (&$isValidDid) {
            $isValidDid = false;
        });

        $handleRule->validate('authority', $authority, function () use (&$isValidHandle) {
            $isValidHandle = false;
        });

        if (! $isValidDid && ! $isValidHandle) {
            return false;
        }

        // If there's a path, validate it
        if (isset($parts[1]) && ! empty($parts[1])) {
            // Path should be collection/rkey format
            $pathParts = explode('/', $parts[1]);

            if (count($pathParts) < 1) {
                return false;
            }

            // Each path segment should be valid
            foreach ($pathParts as $segment) {
                if (empty($segment) || ! $this->isValidPathSegment($segment)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check if path segment is valid.
     */
    protected function isValidPathSegment(string $segment): bool
    {
        // Path segments should be alphanumeric with dots, hyphens, underscores
        return (bool) preg_match('/^[a-zA-Z0-9._-]+$/', $segment);
    }
}
