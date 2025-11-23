<?php

namespace SocialDept\Schema\Validation\TypeValidators;

use SocialDept\Schema\Exceptions\RecordValidationException;
use SocialDept\Schema\Services\UnionResolver;

class UnionValidator
{
    /**
     * Create a new UnionValidator.
     */
    public function __construct(
        protected ?UnionResolver $resolver = null
    ) {
        $this->resolver = $resolver ?? new UnionResolver();
    }

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
            $this->validateDiscriminatedUnion($value, $refs, $path, $definition);
        } else {
            $this->validateOpenUnion($value, $refs, $path);
        }
    }

    /**
     * Validate discriminated (closed) union.
     *
     * @param  array<string>  $refs
     * @param  array<string, mixed>  $definition
     */
    protected function validateDiscriminatedUnion(mixed $value, array $refs, string $path, array $definition): void
    {
        // Delegate validation to UnionResolver which handles all the logic
        try {
            $this->resolver->resolve($value, $definition);
        } catch (RecordValidationException $e) {
            // Re-throw with path context
            throw RecordValidationException::invalidValue(
                $path,
                $e->getMessage()
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
