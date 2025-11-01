<?php

namespace SocialDept\Schema\Parser;

use SocialDept\Schema\Data\TypeDefinition;
use SocialDept\Schema\Data\Types\BooleanType;
use SocialDept\Schema\Data\Types\BytesType;
use SocialDept\Schema\Data\Types\CidLinkType;
use SocialDept\Schema\Data\Types\IntegerType;
use SocialDept\Schema\Data\Types\NullType;
use SocialDept\Schema\Data\Types\StringType;
use SocialDept\Schema\Data\Types\UnknownType;
use SocialDept\Schema\Exceptions\TypeResolutionException;

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
