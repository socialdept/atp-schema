<?php

namespace SocialDept\Schema\Exceptions;

class BlobException extends SchemaException
{
    /**
     * Create exception for upload failure.
     */
    public static function uploadFailed(string $reason): self
    {
        return static::withContext(
            "Blob upload failed: {$reason}",
            ['reason' => $reason]
        );
    }

    /**
     * Create exception for download failure.
     */
    public static function downloadFailed(string $cid, string $reason): self
    {
        return static::withContext(
            "Failed to download blob {$cid}: {$reason}",
            ['cid' => $cid, 'reason' => $reason]
        );
    }

    /**
     * Create exception for invalid MIME type.
     */
    public static function invalidMimeType(string $mimeType, array $accepted): self
    {
        return static::withContext(
            "Invalid MIME type {$mimeType}. Accepted: ".implode(', ', $accepted),
            ['mimeType' => $mimeType, 'accepted' => $accepted]
        );
    }

    /**
     * Create exception for file size violation.
     */
    public static function fileTooLarge(int $size, int $maxSize): self
    {
        return static::withContext(
            "File size {$size} bytes exceeds maximum {$maxSize} bytes",
            ['size' => $size, 'maxSize' => $maxSize]
        );
    }
}
