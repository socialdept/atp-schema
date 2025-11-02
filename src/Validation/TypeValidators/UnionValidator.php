<?php

namespace SocialDept\Schema\Validation\TypeValidators;

use SocialDept\Schema\Exceptions\RecordValidationException;

class UnionValidator
{
    /**
     * Validate a union value against constraints.
     *
     * @param  array<string, mixed>  $definition
     */
    public function validate(mixed $value, array $definition, string $path): void
    {
        $refs = $definition['refs'] ?? [];

        if (empty($refs)) {
            throw RecordValidationException::invalidValue($path, 'Union must have refs defined');
        }

        // Check if union is discriminated (closed)
        $closed = $definition['closed'] ?? false;

        if ($closed) {
            $this->validateDiscriminatedUnion($value, $refs, $path);
        } else {
            $this->validateOpenUnion($value, $refs, $path);
        }
    }

    /**
     * Validate discriminated (closed) union.
     *
     * @param  array<string>  $refs
     */
    protected function validateDiscriminatedUnion(mixed $value, array $refs, string $path): void
    {
        if (! is_array($value)) {
            throw RecordValidationException::invalidType($path, 'object', gettype($value));
        }

        // Check for $type discriminator
        if (! isset($value['$type'])) {
            throw RecordValidationException::invalidValue(
                $path,
                'Discriminated union must have $type field'
            );
        }

        $type = $value['$type'];

        // Validate that $type is one of the allowed refs
        if (! in_array($type, $refs, true)) {
            $allowed = implode(', ', $refs);

            throw RecordValidationException::invalidValue(
                $path,
                "Union type '{$type}' not allowed. Must be one of: {$allowed}"
            );
        }
    }

    /**
     * Validate open (undiscriminated) union.
     *
     * @param  array<string>  $refs
     */
    protected function validateOpenUnion(mixed $value, array $refs, string $path): void
    {
        // For open unions, we just verify it's valid data
        // The actual type checking would require schema resolution which is complex
        // For now, we just ensure it's an object or primitive type

        if (is_null($value)) {
            throw RecordValidationException::invalidValue($path, 'Union value cannot be null');
        }

        // Open unions are flexible, so we allow objects and primitives
        if (! is_array($value) && ! is_string($value) && ! is_int($value) && ! is_bool($value)) {
            throw RecordValidationException::invalidType($path, 'valid union value', gettype($value));
        }
    }
}
