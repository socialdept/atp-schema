<?php

namespace SocialDept\AtpSchema\Validation\TypeValidators;

use SocialDept\AtpSchema\Exceptions\RecordValidationException;

class BlobValidator
{
    /**
     * Validate a blob value against constraints.
     *
     * @param  array<string, mixed>  $definition
     */
    public function validate(mixed $value, array $definition, string $path): void
    {
        // Blob should be an object with specific structure
        if (! is_array($value)) {
            throw RecordValidationException::invalidType($path, 'blob', gettype($value));
        }

        // Check for required blob structure
        if (! isset($value['$type']) || $value['$type'] !== 'blob') {
            throw RecordValidationException::invalidValue(
                $path,
                'Blob must have $type field set to "blob"'
            );
        }

        // Validate ref (CID)
        if (! isset($value['ref'])) {
            throw RecordValidationException::invalidValue($path, 'Blob must have ref field');
        }

        // Validate mimeType
        if (! isset($value['mimeType']) || ! is_string($value['mimeType'])) {
            throw RecordValidationException::invalidValue($path, 'Blob must have valid mimeType');
        }

        // Validate size
        if (! isset($value['size']) || ! is_int($value['size'])) {
            throw RecordValidationException::invalidValue($path, 'Blob must have valid size');
        }

        // Validate MIME type constraint
        if (isset($definition['accept']) && is_array($definition['accept'])) {
            $this->validateMimeType($value['mimeType'], $definition['accept'], $path);
        }

        // Validate size constraints
        if (isset($definition['maxSize'])) {
            if ($value['size'] > $definition['maxSize']) {
                throw RecordValidationException::invalidValue(
                    $path,
                    "Blob size ({$value['size']}) exceeds maximum ({$definition['maxSize']})"
                );
            }
        }
    }

    /**
     * Validate MIME type against accepted types.
     *
     * @param  array<string>  $acceptedTypes
     */
    protected function validateMimeType(string $mimeType, array $acceptedTypes, string $path): void
    {
        foreach ($acceptedTypes as $acceptedType) {
            // Handle wildcards (e.g., image/*)
            if (str_contains($acceptedType, '*')) {
                // Quote everything except the asterisk, then replace * with .*
                $pattern = '/^'.str_replace('\\*', '.*', preg_quote($acceptedType, '/')).'$/';
                if (preg_match($pattern, $mimeType)) {
                    return;
                }
            } elseif ($mimeType === $acceptedType) {
                return;
            }
        }

        $allowed = implode(', ', $acceptedTypes);

        throw RecordValidationException::invalidValue(
            $path,
            "MIME type '{$mimeType}' not accepted. Allowed: {$allowed}"
        );
    }
}
