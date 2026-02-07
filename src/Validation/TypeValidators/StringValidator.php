<?php

namespace SocialDept\AtpSchema\Validation\TypeValidators;

use SocialDept\AtpSchema\Exceptions\RecordValidationException;
use SocialDept\AtpSupport\Identity;
use SocialDept\AtpSupport\Nsid;

class StringValidator
{
    /**
     * Validate a string value against constraints.
     *
     * @param  array<string, mixed>  $definition
     */
    public function validate(mixed $value, array $definition, string $path): void
    {
        if (! is_string($value)) {
            throw RecordValidationException::invalidType($path, 'string', gettype($value));
        }

        // Length constraints
        if (isset($definition['maxLength'])) {
            $length = strlen($value);
            if ($length > $definition['maxLength']) {
                throw RecordValidationException::invalidValue(
                    $path,
                    "String length ({$length}) exceeds maximum ({$definition['maxLength']})"
                );
            }
        }

        if (isset($definition['minLength'])) {
            $length = strlen($value);
            if ($length < $definition['minLength']) {
                throw RecordValidationException::invalidValue(
                    $path,
                    "String length ({$length}) is below minimum ({$definition['minLength']})"
                );
            }
        }

        // Grapheme constraints
        if (isset($definition['maxGraphemes'])) {
            $graphemes = grapheme_strlen($value);
            if ($graphemes > $definition['maxGraphemes']) {
                throw RecordValidationException::invalidValue(
                    $path,
                    "String graphemes ({$graphemes}) exceeds maximum ({$definition['maxGraphemes']})"
                );
            }
        }

        if (isset($definition['minGraphemes'])) {
            $graphemes = grapheme_strlen($value);
            if ($graphemes < $definition['minGraphemes']) {
                throw RecordValidationException::invalidValue(
                    $path,
                    "String graphemes ({$graphemes}) is below minimum ({$definition['minGraphemes']})"
                );
            }
        }

        // Enum constraint
        if (isset($definition['enum']) && is_array($definition['enum'])) {
            if (! in_array($value, $definition['enum'], true)) {
                $allowed = implode(', ', $definition['enum']);

                throw RecordValidationException::invalidValue(
                    $path,
                    "Value must be one of: {$allowed}"
                );
            }
        }

        // Const constraint
        if (isset($definition['const'])) {
            if ($value !== $definition['const']) {
                throw RecordValidationException::invalidValue(
                    $path,
                    "Value must be '{$definition['const']}'"
                );
            }
        }

        // Format validation
        if (isset($definition['format'])) {
            $this->validateFormat($value, $definition['format'], $path);
        }
    }

    /**
     * Validate string format.
     */
    protected function validateFormat(string $value, string $format, string $path): void
    {
        $valid = match ($format) {
            'datetime' => $this->validateDatetime($value),
            'uri' => $this->validateUri($value),
            'at-uri' => $this->validateAtUri($value),
            'did' => $this->validateDid($value),
            'handle' => $this->validateHandle($value),
            'at-identifier' => $this->validateAtIdentifier($value),
            'nsid' => $this->validateNsid($value),
            'cid' => $this->validateCid($value),
            'language' => $this->validateLanguage($value),
            default => true, // Unknown formats pass
        };

        if (! $valid) {
            throw RecordValidationException::invalidValue($path, "Invalid format: {$format}");
        }
    }

    /**
     * Validate datetime format.
     */
    protected function validateDatetime(string $value): bool
    {
        $datetime = \DateTime::createFromFormat(\DateTime::ATOM, $value);
        if ($datetime !== false) {
            return true;
        }

        $datetime = \DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $value);
        if ($datetime !== false) {
            return true;
        }

        $datetime = \DateTime::createFromFormat('Y-m-d\TH:i:s\Z', $value);

        return $datetime !== false;
    }

    /**
     * Validate URI format.
     */
    protected function validateUri(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Validate AT URI format.
     */
    protected function validateAtUri(string $value): bool
    {
        return str_starts_with($value, 'at://') && strlen($value) > 5;
    }

    /**
     * Validate DID format.
     */
    protected function validateDid(string $value): bool
    {
        return Identity::isDid($value);
    }

    /**
     * Validate handle format.
     */
    protected function validateHandle(string $value): bool
    {
        return Identity::isHandle($value);
    }

    /**
     * Validate AT identifier (DID or handle).
     */
    protected function validateAtIdentifier(string $value): bool
    {
        return Identity::isDid($value) || Identity::isHandle($value);
    }

    /**
     * Validate NSID format.
     */
    protected function validateNsid(string $value): bool
    {
        return Nsid::isValid($value);
    }

    /**
     * Validate CID format.
     */
    protected function validateCid(string $value): bool
    {
        // CIDv0 or CIDv1
        if (str_starts_with($value, 'Qm') && strlen($value) === 46) {
            return (bool) preg_match('/^[123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz]+$/', $value);
        }

        if ((str_starts_with($value, 'b') || str_starts_with($value, 'bafy')) && strlen($value) > 10) {
            return (bool) preg_match('/^[a-z2-7]+$/', $value);
        }

        if (str_starts_with($value, 'z') && strlen($value) > 10) {
            return (bool) preg_match('/^[123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz]+$/', $value);
        }

        return false;
    }

    /**
     * Validate language code (BCP 47).
     */
    protected function validateLanguage(string $value): bool
    {
        $pattern = '/^
            ([a-z]{2,3}|[a-z]{4}|[a-z]{5,8})
            (-[A-Z][a-z]{3})?
            (-([A-Z]{2}|[0-9]{3}))?
            (-([a-z0-9]{5,8}|[0-9][a-z0-9]{3}))*
            (-[a-z]-[a-z0-9]{2,8})*
            (-x-[a-z0-9]{1,8})?
        $/xi';

        if (! preg_match($pattern, $value)) {
            return false;
        }

        $primaryLanguage = strtolower(explode('-', $value)[0]);
        $length = strlen($primaryLanguage);

        return $length >= 2 && $length <= 8;
    }
}
