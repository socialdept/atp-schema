<?php

namespace SocialDept\AtpSchema\Data;

use SocialDept\AtpSchema\Exceptions\SchemaValidationException;

class BlobReference
{
    /**
     * CID of the blob.
     */
    public readonly string $ref;

    /**
     * MIME type of the blob.
     */
    public readonly string $mimeType;

    /**
     * Size of the blob in bytes.
     */
    public readonly int $size;

    /**
     * Create a new BlobReference.
     */
    public function __construct(
        string $ref,
        string $mimeType,
        int $size
    ) {
        $this->ref = $ref;
        $this->mimeType = $mimeType;
        $this->size = $size;
    }

    /**
     * Create from array data.
     */
    public static function fromArray(array $data): self
    {
        // Extract ref - can be a string or array with $link
        $ref = null;
        if (isset($data['ref'])) {
            if (is_string($data['ref'])) {
                $ref = $data['ref'];
            } elseif (is_array($data['ref']) && isset($data['ref']['$link'])) {
                $ref = $data['ref']['$link'];
            }
        }

        if ($ref === null) {
            throw SchemaValidationException::missingField('blob', 'ref');
        }

        if (! isset($data['mimeType'])) {
            throw SchemaValidationException::missingField('blob', 'mimeType');
        }

        if (! isset($data['size'])) {
            throw SchemaValidationException::missingField('blob', 'size');
        }

        return new self(
            ref: $ref,
            mimeType: $data['mimeType'],
            size: (int) $data['size']
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            '$type' => 'blob',
            'ref' => [
                '$link' => $this->ref,
            ],
            'mimeType' => $this->mimeType,
            'size' => $this->size,
        ];
    }

    /**
     * Get the CID.
     */
    public function getCid(): string
    {
        return $this->ref;
    }

    /**
     * Get the MIME type.
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * Get the size in bytes.
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Check if blob is an image.
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mimeType, 'image/');
    }

    /**
     * Check if blob is a video.
     */
    public function isVideo(): bool
    {
        return str_starts_with($this->mimeType, 'video/');
    }

    /**
     * Check if blob matches a MIME type pattern.
     */
    public function matchesMimeType(string $pattern): bool
    {
        if (str_contains($pattern, '*')) {
            $regex = '/^'.str_replace('\\*', '.*', preg_quote($pattern, '/')).'$/';

            return (bool) preg_match($regex, $this->mimeType);
        }

        return $this->mimeType === $pattern;
    }
}
