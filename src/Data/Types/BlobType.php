<?php

namespace SocialDept\Schema\Data\Types;

use SocialDept\Schema\Data\TypeDefinition;
use SocialDept\Schema\Exceptions\RecordValidationException;

class BlobType extends TypeDefinition
{
    /**
     * Accepted MIME types.
     *
     * @var array<string>|null
     */
    public readonly ?array $accept;

    /**
     * Maximum blob size in bytes.
     */
    public readonly ?int $maxSize;

    /**
     * Create a new BlobType.
     *
     * @param  array<string>|null  $accept
     */
    public function __construct(
        ?array $accept = null,
        ?int $maxSize = null,
        ?string $description = null
    ) {
        parent::__construct('blob', $description);

        $this->accept = $accept;
        $this->maxSize = $maxSize;
    }

    /**
     * Create from array data.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            accept: $data['accept'] ?? null,
            maxSize: $data['maxSize'] ?? null,
            description: $data['description'] ?? null
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        $array = ['type' => $this->type];

        if ($this->description !== null) {
            $array['description'] = $this->description;
        }

        if ($this->accept !== null) {
            $array['accept'] = $this->accept;
        }

        if ($this->maxSize !== null) {
            $array['maxSize'] = $this->maxSize;
        }

        return $array;
    }

    /**
     * Validate a value against this type definition.
     */
    public function validate(mixed $value, string $path = ''): void
    {
        if (! is_array($value)) {
            throw RecordValidationException::invalidType($path, 'blob (object)', gettype($value));
        }

        // Blob must have $type property
        if (! isset($value['$type']) || $value['$type'] !== 'blob') {
            throw RecordValidationException::invalidValue($path, 'must have $type property set to "blob"');
        }

        // Blob must have ref (CID reference)
        if (! isset($value['ref'])) {
            throw RecordValidationException::invalidValue($path, 'must have ref property');
        }

        // Blob must have mimeType
        if (! isset($value['mimeType'])) {
            throw RecordValidationException::invalidValue($path, 'must have mimeType property');
        }

        // Blob must have size
        if (! isset($value['size'])) {
            throw RecordValidationException::invalidValue($path, 'must have size property');
        }

        // Validate MIME type if accept is specified
        if ($this->accept !== null) {
            $mimeType = $value['mimeType'];

            if (! $this->isMimeTypeAccepted($mimeType)) {
                $accepted = implode(', ', $this->accept);

                throw RecordValidationException::invalidValue($path, "MIME type must be one of: {$accepted}");
            }
        }

        // Validate size if maxSize is specified
        if ($this->maxSize !== null) {
            $size = $value['size'];

            if (! is_int($size)) {
                throw RecordValidationException::invalidValue($path, 'size must be an integer');
            }

            if ($size > $this->maxSize) {
                throw RecordValidationException::invalidValue($path, "size must not exceed {$this->maxSize} bytes");
            }
        }
    }

    /**
     * Check if a MIME type is accepted.
     */
    protected function isMimeTypeAccepted(string $mimeType): bool
    {
        if ($this->accept === null) {
            return true;
        }

        foreach ($this->accept as $accepted) {
            // Exact match
            if ($accepted === $mimeType) {
                return true;
            }

            // Wildcard match (e.g., image/*)
            if (str_ends_with($accepted, '/*')) {
                $prefix = substr($accepted, 0, -1);
                if (str_starts_with($mimeType, $prefix)) {
                    return true;
                }
            }
        }

        return false;
    }
}
