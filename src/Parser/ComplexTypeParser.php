<?php

namespace SocialDept\Schema\Parser;

use SocialDept\Schema\Data\TypeDefinition;
use SocialDept\Schema\Data\Types\ArrayType;
use SocialDept\Schema\Data\Types\BlobType;
use SocialDept\Schema\Data\Types\ObjectType;
use SocialDept\Schema\Data\Types\RefType;
use SocialDept\Schema\Data\Types\UnionType;
use SocialDept\Schema\Exceptions\TypeResolutionException;

class ComplexTypeParser
{
    /**
     * Primitive parser for nested types.
     */
    protected PrimitiveParser $primitiveParser;

    /**
     * Create a new ComplexTypeParser.
     */
    public function __construct(?PrimitiveParser $primitiveParser = null)
    {
        $this->primitiveParser = $primitiveParser ?? new PrimitiveParser();
    }

    /**
     * Parse a complex type definition from array data.
     *
     * @throws TypeResolutionException
     */
    public function parse(array $data): TypeDefinition
    {
        $type = $data['type'] ?? null;

        if ($type === null) {
            throw TypeResolutionException::unknownType('(missing type field)');
        }

        return match ($type) {
            'object' => $this->parseObject($data),
            'array' => $this->parseArray($data),
            'union' => UnionType::fromArray($data),
            'ref' => RefType::fromArray($data),
            'blob' => BlobType::fromArray($data),
            default => throw TypeResolutionException::unknownType($type),
        };
    }

    /**
     * Parse an object type with nested properties.
     */
    protected function parseObject(array $data): ObjectType
    {
        $object = ObjectType::fromArray($data);

        // Parse properties if present
        if (isset($data['properties']) && is_array($data['properties'])) {
            $properties = [];

            foreach ($data['properties'] as $key => $propertyData) {
                $properties[$key] = $this->parseNestedType($propertyData);
            }

            $object = $object->withProperties($properties);
        }

        return $object;
    }

    /**
     * Parse an array type with nested items.
     */
    protected function parseArray(array $data): ArrayType
    {
        $array = ArrayType::fromArray($data);

        // Parse items if present
        if (isset($data['items']) && is_array($data['items'])) {
            $items = $this->parseNestedType($data['items']);
            $array = $array->withItems($items);
        }

        return $array;
    }

    /**
     * Parse a nested type definition (can be primitive or complex).
     */
    protected function parseNestedType(array $data): TypeDefinition
    {
        $type = $data['type'] ?? null;

        if ($type === null) {
            throw TypeResolutionException::unknownType('(missing type field)');
        }

        // Try primitive types first
        if ($this->primitiveParser->isPrimitive($type)) {
            return $this->primitiveParser->parse($data);
        }

        // Try complex types
        return $this->parse($data);
    }

    /**
     * Check if a type is a complex type.
     */
    public function isComplex(string $type): bool
    {
        return in_array($type, [
            'object',
            'array',
            'union',
            'ref',
            'blob',
        ]);
    }

    /**
     * Get all supported complex types.
     *
     * @return array<string>
     */
    public function getSupportedTypes(): array
    {
        return [
            'object',
            'array',
            'union',
            'ref',
            'blob',
        ];
    }
}
