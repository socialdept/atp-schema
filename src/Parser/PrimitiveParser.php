<?php

namespace SocialDept\AtpSchema\Parser;

use SocialDept\AtpSchema\Data\TypeDefinition;
use SocialDept\AtpSchema\Data\Types\BooleanType;
use SocialDept\AtpSchema\Data\Types\BytesType;
use SocialDept\AtpSchema\Data\Types\CidLinkType;
use SocialDept\AtpSchema\Data\Types\IntegerType;
use SocialDept\AtpSchema\Data\Types\NullType;
use SocialDept\AtpSchema\Data\Types\StringType;
use SocialDept\AtpSchema\Data\Types\UnknownType;
use SocialDept\AtpSchema\Exceptions\TypeResolutionException;

class PrimitiveParser
{
    /**
     * Parse a primitive type definition from array data.
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
            'null' => NullType::fromArray($data),
            'boolean' => BooleanType::fromArray($data),
            'integer' => IntegerType::fromArray($data),
            'string' => StringType::fromArray($data),
            'bytes' => BytesType::fromArray($data),
            'cid-link' => CidLinkType::fromArray($data),
            'unknown' => UnknownType::fromArray($data),
            default => throw TypeResolutionException::unknownType($type),
        };
    }

    /**
     * Check if a type is a primitive type.
     */
    public function isPrimitive(string $type): bool
    {
        return in_array($type, [
            'null',
            'boolean',
            'integer',
            'string',
            'bytes',
            'cid-link',
            'unknown',
        ]);
    }

    /**
     * Get all supported primitive types.
     *
     * @return array<string>
     */
    public function getSupportedTypes(): array
    {
        return [
            'null',
            'boolean',
            'integer',
            'string',
            'bytes',
            'cid-link',
            'unknown',
        ];
    }
}
