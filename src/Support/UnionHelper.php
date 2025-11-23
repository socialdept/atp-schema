<?php

namespace SocialDept\Schema\Support;

use InvalidArgumentException;
use SocialDept\Schema\Contracts\DiscriminatedUnion;
use SocialDept\Schema\Data\Data;

/**
 * Helper for resolving discriminated unions based on $type field.
 *
 * This class uses the DiscriminatedUnion interface to build type maps
 * and resolve union data to the correct variant class.
 */
class UnionHelper
{
    /**
     * Resolve a closed union to the correct variant class.
     *
     * @param  array  $data  The union data containing a $type field
     * @param  array<class-string<Data>>  $variants  Array of possible variant class names
     * @return Data The resolved variant instance
     *
     * @throws InvalidArgumentException If $type is missing or unknown
     */
    public static function resolveClosedUnion(array $data, array $variants): Data
    {
        // Validate $type field exists
        if (! isset($data['$type'])) {
            throw new InvalidArgumentException(
                'Closed union data must contain a $type field for discrimination'
            );
        }

        $type = $data['$type'];

        // Build type map using DiscriminatedUnion interface
        $typeMap = static::buildTypeMap($variants);

        // Check if type is known
        if (! isset($typeMap[$type])) {
            $knownTypes = implode(', ', array_keys($typeMap));
            throw new InvalidArgumentException(
                "Unknown union type '{$type}'. Expected one of: {$knownTypes}"
            );
        }

        // Resolve to correct variant class
        $class = $typeMap[$type];

        return $class::fromArray($data);
    }

    /**
     * Validate an open union has $type field.
     *
     * Open unions pass data through as-is but must have $type for future discrimination.
     *
     * @param  array  $data  The union data
     * @return array The validated union data
     *
     * @throws InvalidArgumentException If $type is missing
     */
    public static function validateOpenUnion(array $data): array
    {
        if (! isset($data['$type'])) {
            throw new InvalidArgumentException(
                'Open union data must contain a $type field for future discrimination'
            );
        }

        return $data;
    }

    /**
     * Build a type map from variant classes using DiscriminatedUnion interface.
     *
     * @param  array<class-string<Data>>  $variants  Array of variant class names
     * @return array<string, class-string<Data>> Map of discriminator => class name
     */
    protected static function buildTypeMap(array $variants): array
    {
        $typeMap = [];

        foreach ($variants as $class) {
            // Ensure class implements DiscriminatedUnion
            if (! is_subclass_of($class, DiscriminatedUnion::class)) {
                throw new InvalidArgumentException(
                    "Variant class {$class} must implement DiscriminatedUnion interface"
                );
            }

            // Get discriminator from the class
            $discriminator = $class::getDiscriminator();
            $typeMap[$discriminator] = $class;
        }

        return $typeMap;
    }
}
