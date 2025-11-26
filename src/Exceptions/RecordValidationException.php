<?php

namespace SocialDept\AtpSchema\Exceptions;

class RecordValidationException extends SchemaException
{
    /**
     * Validation errors.
     */
    protected array $errors = [];

    /**
     * Create exception with validation errors.
     */
    public static function withErrors(string $nsid, array $errors): self
    {
        $instance = new static("Record validation failed for {$nsid}");
        $instance->errors = $errors;
        $instance->setContext(['nsid' => $nsid, 'errors' => $errors]);

        return $instance;
    }

    /**
     * Get validation errors.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Create exception for type mismatch.
     */
    public static function typeMismatch(string $field, string $expected, string $actual): self
    {
        return static::withContext(
            "Type mismatch for field {$field}: expected {$expected}, got {$actual}",
            ['field' => $field, 'expected' => $expected, 'actual' => $actual]
        );
    }

    /**
     * Create exception for constraint violation.
     */
    public static function constraintViolation(string $field, string $constraint, mixed $value): self
    {
        return static::withContext(
            "Constraint violation for field {$field}: {$constraint}",
            ['field' => $field, 'constraint' => $constraint, 'value' => $value]
        );
    }

    /**
     * Create exception for invalid type.
     */
    public static function invalidType(string $path, string $expected, string $actual): self
    {
        return static::withContext(
            "Expected type '{$expected}' at '{$path}' but got '{$actual}'",
            ['path' => $path, 'expected' => $expected, 'actual' => $actual]
        );
    }

    /**
     * Create exception for invalid value.
     */
    public static function invalidValue(string $path, string $reason): self
    {
        return static::withContext(
            "Invalid value at '{$path}': {$reason}",
            ['path' => $path, 'reason' => $reason]
        );
    }
}
