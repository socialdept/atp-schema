<?php

namespace SocialDept\AtpSchema\Exceptions;

class SchemaNotFoundException extends SchemaException
{
    /**
     * Create exception for missing NSID.
     */
    public static function forNsid(string $nsid): self
    {
        return static::withContext(
            "Schema not found for NSID: {$nsid}",
            ['nsid' => $nsid]
        );
    }

    /**
     * Create exception for missing file.
     */
    public static function forFile(string $path): self
    {
        return static::withContext(
            "Schema file not found: {$path}",
            ['path' => $path]
        );
    }
}
