<?php

namespace SocialDept\AtpSchema\Exceptions;

class SchemaParseException extends SchemaException
{
    /**
     * Create exception for invalid JSON.
     */
    public static function invalidJson(string $nsid, string $error): self
    {
        return static::withContext(
            "Failed to parse schema JSON for {$nsid}: {$error}",
            ['nsid' => $nsid, 'error' => $error]
        );
    }

    /**
     * Create exception for malformed schema.
     */
    public static function malformed(string $nsid, string $reason): self
    {
        return static::withContext(
            "Malformed schema for {$nsid}: {$reason}",
            ['nsid' => $nsid, 'reason' => $reason]
        );
    }
}
