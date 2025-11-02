<?php

namespace SocialDept\Schema\Validation;

class ValidationErrorFormatter
{
    /**
     * Format errors for Laravel ValidationException.
     *
     * @param  array<ValidationError>  $errors
     * @return array<string, array<string>>
     */
    public function formatForLaravel(array $errors): array
    {
        $formatted = [];

        foreach ($errors as $error) {
            $field = $this->convertFieldPath($error->getField());

            if (! isset($formatted[$field])) {
                $formatted[$field] = [];
            }

            $formatted[$field][] = $error->getMessage();
        }

        return $formatted;
    }

    /**
     * Format errors as flat array of messages.
     *
     * @param  array<ValidationError>  $errors
     * @return array<string>
     */
    public function formatAsMessages(array $errors): array
    {
        $messages = [];

        foreach ($errors as $error) {
            $messages[] = $error->getMessage();
        }

        return $messages;
    }

    /**
     * Format errors with field context.
     *
     * @param  array<ValidationError>  $errors
     * @return array<string>
     */
    public function formatWithFields(array $errors): array
    {
        $messages = [];

        foreach ($errors as $error) {
            $messages[] = $error->getField().': '.$error->getMessage();
        }

        return $messages;
    }

    /**
     * Format errors as detailed array.
     *
     * @param  array<ValidationError>  $errors
     * @return array<array<string, mixed>>
     */
    public function formatDetailed(array $errors): array
    {
        $formatted = [];

        foreach ($errors as $error) {
            $formatted[] = $error->toArray();
        }

        return $formatted;
    }

    /**
     * Group errors by field.
     *
     * @param  array<ValidationError>  $errors
     * @return array<string, array<ValidationError>>
     */
    public function groupByField(array $errors): array
    {
        $grouped = [];

        foreach ($errors as $error) {
            $field = $error->getField();

            if (! isset($grouped[$field])) {
                $grouped[$field] = [];
            }

            $grouped[$field][] = $error;
        }

        return $grouped;
    }

    /**
     * Convert field path from dot notation to Laravel format.
     */
    protected function convertFieldPath(string $path): string
    {
        // Remove leading $. if present
        if (str_starts_with($path, '$.')) {
            $path = substr($path, 2);
        } elseif ($path === '$') {
            return '_root';
        }

        // Convert array notation from [0] to .0
        $path = preg_replace('/\[(\d+)\]/', '.$1', $path);

        return $path;
    }

    /**
     * Format a single error.
     */
    public function formatError(ValidationError $error): string
    {
        $message = $error->getMessage();

        if ($error->hasRule()) {
            $message .= " (Rule: {$error->getRule()})";
        }

        if ($error->hasExpected() && $error->hasActual()) {
            $expected = $this->formatValue($error->getExpected());
            $actual = $this->formatValue($error->getActual());
            $message .= " [Expected: {$expected}, Got: {$actual}]";
        }

        return $message;
    }

    /**
     * Format a value for display.
     */
    protected function formatValue(mixed $value): string
    {
        if (is_null($value)) {
            return 'null';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_array($value)) {
            return 'array('.count($value).')';
        }

        if (is_object($value)) {
            return 'object('.get_class($value).')';
        }

        if (is_string($value) && strlen($value) > 50) {
            return substr($value, 0, 50).'...';
        }

        return (string) $value;
    }

    /**
     * Create human-readable summary.
     *
     * @param  array<ValidationError>  $errors
     */
    public function createSummary(array $errors): string
    {
        $count = count($errors);

        if ($count === 0) {
            return 'No validation errors';
        }

        if ($count === 1) {
            return 'Validation failed: '.$errors[0]->getMessage();
        }

        $fields = array_unique(array_map(fn ($error) => $error->getField(), $errors));
        $fieldCount = count($fields);

        return "Validation failed with {$count} errors across {$fieldCount} fields";
    }

    /**
     * Format errors as JSON string.
     *
     * @param  array<ValidationError>  $errors
     */
    public function toJson(array $errors, int $options = 0): string
    {
        return json_encode($this->formatDetailed($errors), $options);
    }

    /**
     * Format errors as pretty JSON string.
     *
     * @param  array<ValidationError>  $errors
     */
    public function toPrettyJson(array $errors): string
    {
        return $this->toJson($errors, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
