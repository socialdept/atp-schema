<?php

namespace SocialDept\Schema\Validation\TypeValidators;

use SocialDept\Schema\Exceptions\RecordValidationException;

class ArrayValidator
{
    /**
     * Validate an array value against constraints.
     *
     * @param  array<string, mixed>  $definition
     */
    public function validate(mixed $value, array $definition, string $path): void
    {
        if (! is_array($value)) {
            throw RecordValidationException::invalidType($path, 'array', gettype($value));
        }

        $count = count($value);

        // MaxItems constraint
        if (isset($definition['maxItems'])) {
            if ($count > $definition['maxItems']) {
                throw RecordValidationException::invalidValue(
                    $path,
                    "Array length ({$count}) exceeds maximum ({$definition['maxItems']})"
                );
            }
        }

        // MinItems constraint
        if (isset($definition['minItems'])) {
            if ($count < $definition['minItems']) {
                throw RecordValidationException::invalidValue(
                    $path,
                    "Array length ({$count}) is below minimum ({$definition['minItems']})"
                );
            }
        }

        // Validate items if item schema is provided
        if (isset($definition['items']) && is_array($definition['items'])) {
            $this->validateItems($value, $definition['items'], $path);
        }
    }

    /**
     * Validate array items.
     *
     * @param  array<mixed>  $items
     * @param  array<string, mixed>  $itemDefinition
     */
    protected function validateItems(array $items, array $itemDefinition, string $path): void
    {
        $itemType = $itemDefinition['type'] ?? null;

        if ($itemType === null) {
            return;
        }

        foreach ($items as $index => $item) {
            $itemPath = "{$path}[{$index}]";
            $this->validateItem($item, $itemDefinition, $itemPath);
        }
    }

    /**
     * Validate a single array item.
     *
     * @param  array<string, mixed>  $definition
     */
    protected function validateItem(mixed $value, array $definition, string $path): void
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
