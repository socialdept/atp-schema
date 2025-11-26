<?php

namespace SocialDept\AtpSchema\Validation\TypeValidators;

use SocialDept\AtpSchema\Exceptions\RecordValidationException;

class BooleanValidator
{
    /**
     * Validate a boolean value.
     *
     * @param  array<string, mixed>  $definition
     */
    public function validate(mixed $value, array $definition, string $path): void
    {
        if (! is_bool($value)) {
            throw RecordValidationException::invalidType($path, 'boolean', gettype($value));
        }

        // Const constraint
        if (isset($definition['const'])) {
            if ($value !== $definition['const']) {
                $expectedValue = $definition['const'] ? 'true' : 'false';

                throw RecordValidationException::invalidValue(
                    $path,
                    "Value must be {$expectedValue}"
                );
            }
        }
    }
}
