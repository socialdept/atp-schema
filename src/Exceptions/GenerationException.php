<?php

namespace SocialDept\Schema\Exceptions;

class GenerationException extends SchemaException
{
    /**
     * Create exception for generation failure.
     */
    public static function failed(string $nsid, string $reason): self
    {
        return static::withContext(
            "Failed to generate Data class for {$nsid}: {$reason}",
            ['nsid' => $nsid, 'reason' => $reason]
        );
    }

    /**
     * Create exception for file write failure.
     */
    public static function fileWriteFailed(string $path, string $error): self
    {
        return static::withContext(
            "Failed to write generated file to {$path}: {$error}",
            ['path' => $path, 'error' => $error]
        );
    }

    /**
     * Create exception for unsupported feature.
     */
    public static function unsupportedFeature(string $nsid, string $feature): self
    {
        return static::withContext(
            "Unsupported feature in schema {$nsid}: {$feature}",
            ['nsid' => $nsid, 'feature' => $feature]
        );
    }
}
