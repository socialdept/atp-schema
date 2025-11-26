<?php

namespace SocialDept\AtpSchema\Data\Types;

use SocialDept\AtpSchema\Data\TypeDefinition;
use SocialDept\AtpSchema\Exceptions\RecordValidationException;

class BytesType extends TypeDefinition
{
    /**
     * Minimum byte length.
     */
    public readonly ?int $minLength;

    /**
     * Maximum byte length.
     */
    public readonly ?int $maxLength;

    /**
     * Create a new BytesType.
     */
    public function __construct(
        ?string $description = null,
        ?int $minLength = null,
        ?int $maxLength = null
    ) {
        parent::__construct('bytes', $description);

        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
    }

    /**
     * Create from array data.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            description: $data['description'] ?? null,
            minLength: $data['minLength'] ?? null,
            maxLength: $data['maxLength'] ?? null
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

        if ($this->minLength !== null) {
            $array['minLength'] = $this->minLength;
        }

        if ($this->maxLength !== null) {
            $array['maxLength'] = $this->maxLength;
        }

        return $array;
    }

    /**
     * Validate a value against this type definition.
     */
    public function validate(mixed $value, string $path = ''): void
    {
        if (! is_string($value)) {
            throw RecordValidationException::invalidType($path, 'bytes (base64 string)', gettype($value));
        }

        // Validate base64 encoding
        $decoded = base64_decode($value, true);

        if ($decoded === false || base64_encode($decoded) !== $value) {
            throw RecordValidationException::invalidValue($path, 'must be valid base64-encoded data');
        }

        // Length validation on decoded bytes
        $length = strlen($decoded);

        if ($this->minLength !== null && $length < $this->minLength) {
            throw RecordValidationException::invalidValue($path, "must be at least {$this->minLength} bytes");
        }

        if ($this->maxLength !== null && $length > $this->maxLength) {
            throw RecordValidationException::invalidValue($path, "must be at most {$this->maxLength} bytes");
        }
    }
}
