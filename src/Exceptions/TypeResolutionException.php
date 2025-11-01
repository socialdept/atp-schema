<?php

namespace SocialDept\Schema\Exceptions;

class TypeResolutionException extends SchemaException
{
    /**
     * Create exception for unknown type.
     */
    public static function unknownType(string $type): self
    {
        return static::withContext(
            "Unknown Lexicon type: {$type}",
            ['type' => $type]
        );
    }

    /**
     * Create exception for unresolvable reference.
     */
    public static function unresolv ableReference(string $ref, string $nsid): self
    {
        return static::withContext(
            "Cannot resolve reference {$ref} in schema {$nsid}",
            ['ref' => $ref, 'nsid' => $nsid]
        );
    }

    /**
     * Create exception for circular reference.
     */
    public static function circularReference(string $ref, array $chain): self
    {
        return static::withContext(
            "Circular reference detected: {$ref}",
            ['ref' => $ref, 'chain' => $chain]
        );
    }
}
