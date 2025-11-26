<?php

namespace SocialDept\AtpSchema\Exceptions;

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

    /**
     * Create exception for template not found.
     */
    public static function templateNotFound(string $templateName): self
    {
        return static::withContext(
            "Template not found: {$templateName}",
            ['template' => $templateName]
        );
    }

    /**
     * Create exception for file already exists.
     */
    public static function fileExists(string $path): self
    {
        return static::withContext(
            "File already exists: {$path}",
            ['path' => $path]
        );
    }

    /**
     * Create exception for directory not found.
     */
    public static function directoryNotFound(string $directory): self
    {
        return static::withContext(
            "Directory not found: {$directory}",
            ['directory' => $directory]
        );
    }

    /**
     * Create exception for cannot create directory.
     */
    public static function cannotCreateDirectory(string $directory): self
    {
        return static::withContext(
            "Cannot create directory: {$directory}",
            ['directory' => $directory]
        );
    }

    /**
     * Create exception for cannot write file.
     */
    public static function cannotWriteFile(string $path): self
    {
        return static::withContext(
            "Cannot write file: {$path}",
            ['path' => $path]
        );
    }

    /**
     * Create exception for cannot delete file.
     */
    public static function cannotDeleteFile(string $path): self
    {
        return static::withContext(
            "Cannot delete file: {$path}",
            ['path' => $path]
        );
    }

    /**
     * Create exception for file not found.
     */
    public static function fileNotFound(string $path): self
    {
        return static::withContext(
            "File not found: {$path}",
            ['path' => $path]
        );
    }

    /**
     * Create exception for cannot read file.
     */
    public static function cannotReadFile(string $path): self
    {
        return static::withContext(
            "Cannot read file: {$path}",
            ['path' => $path]
        );
    }
}
