<?php

namespace SocialDept\AtpSchema\Validation\TypeValidators;

use SocialDept\AtpSchema\Exceptions\RecordValidationException;

class IntegerValidator
{
    /**
     * Validate an integer value against constraints.
     *
     * @param  array<string, mixed>  $definition
     */
    public function validate(mixed $value, array $definition, string $path): void
    {
        if (! is_int($value)) {
            throw RecordValidationException::invalidType($path, 'integer', gettype($value));
        }

        // Maximum constraint
        if (isset($definition['maximum'])) {
            if ($value > $definition['maximum']) {
                throw RecordValidationException::invalidValue(
                    $path,
                    "Value ({$value}) exceeds maximum ({$definition['maximum']})"
                );
            }
        }

        // Minimum constraint
        if (isset($definition['minimum'])) {
            if ($value < $definition['minimum']) {
                throw RecordValidationException::invalidValue(
                    $path,
                    "Value ({$value}) is below minimum ({$definition['minimum']})"
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
                    "Value must be {$definition['const']}"
                );
            }
        }
    }
}
