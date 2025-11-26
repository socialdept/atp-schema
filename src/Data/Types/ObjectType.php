<?php

namespace SocialDept\AtpSchema\Data\Types;

use SocialDept\AtpSchema\Data\TypeDefinition;
use SocialDept\AtpSchema\Exceptions\RecordValidationException;

class ObjectType extends TypeDefinition
{
    /**
     * Object properties.
     *
     * @var array<string, TypeDefinition>
     */
    public readonly array $properties;

    /**
     * Required property names.
     *
     * @var array<string>
     */
    public readonly array $required;

    /**
     * Whether nullable properties are allowed.
     */
    public readonly bool $nullable;

    /**
     * Create a new ObjectType.
     *
     * @param  array<string, TypeDefinition>  $properties
     * @param  array<string>  $required
     */
    public function __construct(
        array $properties = [],
        array $required = [],
        bool $nullable = false,
        ?string $description = null
    ) {
        parent::__construct('object', $description);

        $this->properties = $properties;
        $this->required = $required;
        $this->nullable = $nullable;
    }

    /**
     * Create from array data.
     */
    public static function fromArray(array $data): self
    {
        // Properties will be parsed by TypeParser, this is just a placeholder
        return new self(
            properties: [],
            required: $data['required'] ?? [],
            nullable: $data['nullable'] ?? false,
            description: $data['description'] ?? null
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        $array = ['type' => $this->type];

        if ($this->description !== null) {
            $array['description'] = $this->description;
        }

        if (! empty($this->properties)) {
            $array['properties'] = array_map(
                fn (TypeDefinition $type) => $type->toArray(),
                $this->properties
            );
        }

        if (! empty($this->required)) {
            $array['required'] = $this->required;
        }

        if ($this->nullable) {
            $array['nullable'] = $this->nullable;
        }

        return $array;
    }

    /**
     * Validate a value against this type definition.
     */
    public function validate(mixed $value, string $path = ''): void
    {
        if (! is_array($value)) {
            throw RecordValidationException::invalidType($path, 'object', gettype($value));
        }

        // Validate required properties
        foreach ($this->required as $requiredKey) {
            if (! array_key_exists($requiredKey, $value)) {
                throw RecordValidationException::invalidValue($path, "missing required property '{$requiredKey}'");
            }
        }

        // Validate each property
        foreach ($this->properties as $key => $propertyType) {
            if (! array_key_exists($key, $value)) {
                continue;
            }

            $propertyPath = $path ? "{$path}.{$key}" : $key;
            $propertyValue = $value[$key];

            // Handle nullable
            if ($propertyValue === null && $this->nullable) {
                continue;
            }

            $propertyType->validate($propertyValue, $propertyPath);
        }
    }

    /**
     * Set properties after construction.
     *
     * @param  array<string, TypeDefinition>  $properties
     */
    public function withProperties(array $properties): self
    {
        return new self(
            properties: $properties,
            required: $this->required,
            nullable: $this->nullable,
            description: $this->description
        );
    }
}
