<?php

namespace SocialDept\AtpSchema\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Cid implements ValidationRule
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

        if (! $this->isValidCid($value)) {
            $fail("The {$attribute} is not a valid CID.");
        }
    }

    /**
     * Validate CID format.
     *
     * CID (Content Identifier) is typically base58 or base32 encoded
     * CIDv0: Qm... (base58, 46 characters)
     * CIDv1: b... (base32) or z... (base58)
     */
    protected function isValidCid(string $value): bool
    {
        $length = strlen($value);

        // CIDv0: Starts with Qm and is 46 characters
        if (str_starts_with($value, 'Qm') && $length === 46) {
            return $this->isBase58($value);
        }

        // CIDv1: Starts with 'b' (base32) or 'z' (base58)
        if (str_starts_with($value, 'b') && $length > 10) {
            return $this->isBase32($value);
        }

        if (str_starts_with($value, 'z') && $length > 10) {
            return $this->isBase58($value);
        }

        // Also accept bafy... (base32 CIDv1)
        if (str_starts_with($value, 'bafy') && $length > 10) {
            return $this->isBase32($value);
        }

        return false;
    }

    /**
     * Check if string is valid base58.
     */
    protected function isBase58(string $value): bool
    {
        // Base58 alphabet (no 0, O, I, l)
        return (bool) preg_match('/^[123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz]+$/', $value);
    }

    /**
     * Check if string is valid base32.
     */
    protected function isBase32(string $value): bool
    {
        // Base32 lowercase alphabet
        return (bool) preg_match('/^[a-z2-7]+$/', $value);
    }
}
