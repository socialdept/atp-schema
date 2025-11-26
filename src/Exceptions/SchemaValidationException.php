<?php

namespace SocialDept\AtpSchema\Exceptions;

class SchemaValidationException extends SchemaException
{
    /**
     * Create exception for invalid schema structure.
     */
    public static function invalidStructure(string $nsid, array $errors): self
    {
        $message = "Schema validation failed for {$nsid}:\n".implode("\n", $errors);

        return static::withContext($message, [
            'nsid' => $nsid,
            'errors' => $errors,
        ]);
    }

    /**
     * Create exception for missing required field.
     */
    public static function missingField(string $nsid, string $field): self
    {
        return static::withContext(
            "Required field missing in schema {$nsid}: {$field}",
            ['nsid' => $nsid, 'field' => $field]
        );
    }

    /**
     * Create exception for invalid lexicon version.
     */
    public static function invalidVersion(string $nsid, int $version): self
    {
        return static::withContext(
            "Unsupported lexicon version {$version} in schema {$nsid}",
            ['nsid' => $nsid, 'version' => $version]
        );
    }
}
