<?php

namespace SocialDept\Schema\Validation\TypeValidators;

use SocialDept\Schema\Exceptions\RecordValidationException;

class ObjectValidator
{
    /**
     * Validate an object value against constraints.
     *
     * @param  array<string, mixed>  $definition
     */
    public function validate(mixed $value, array $definition, string $path): void
    {
        if (! is_array($value)) {
            throw RecordValidationException::invalidType($path, 'object', gettype($value));
        }

        $properties = $definition['properties'] ?? [];
        $required = $definition['required'] ?? [];

        // Validate required fields
        foreach ($required as $field) {
            if (! array_key_exists($field, $value)) {
                throw RecordValidationException::invalidValue(
                    "{$path}.{$field}",
                    'Required field is missing'
                );
            }
        }

        // Validate properties
        foreach ($properties as $name => $propDef) {
            if (array_key_exists($name, $value)) {
                $this->validateProperty($value[$name], $propDef, "{$path}.{$name}");
            }
        }
    }

    /**
     * Validate a single property.
     *
     * @param  array<string, mixed>  $definition
     */
    protected function validateProperty(mixed $value, array $definition, string $path): void
    {
        $type = $definition['type'] ?? null;

        $validator = match ($type) {
            'string' => new StringValidator(),
            'integer' => new IntegerValidator(),
            'boolean' => new BooleanValidator(),
            'object' => new ObjectValidator(),
            'array' => new ArrayValidator(),
            default => null,
        };

        if ($validator !== null) {
            $validator->validate($value, $definition, $path);
        }
    }
}
