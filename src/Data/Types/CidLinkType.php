<?php

namespace SocialDept\Schema\Data\Types;

use SocialDept\Schema\Data\TypeDefinition;
use SocialDept\Schema\Exceptions\RecordValidationException;

class CidLinkType extends TypeDefinition
{
    /**
     * Create a new CidLinkType.
     */
    public function __construct(?string $description = null)
    {
        parent::__construct('cid-link', $description);
    }

    /**
     * Create from array data.
     */
    public static function fromArray(array $data): self
    {
        return new self(
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

        return $array;
    }

    /**
     * Validate a value against this type definition.
     */
    public function validate(mixed $value, string $path = ''): void
    {
        if (! is_array($value)) {
            throw RecordValidationException::invalidType($path, 'cid-link (object with $link)', gettype($value));
        }

        if (! isset($value['$link'])) {
            throw RecordValidationException::invalidValue($path, 'must contain $link property');
        }

        $link = $value['$link'];

        if (! is_string($link)) {
            throw RecordValidationException::invalidValue($path, '$link must be a string');
        }

        // Basic CID validation
        if (! preg_match('/^[a-zA-Z0-9]+$/', $link)) {
            throw RecordValidationException::invalidValue($path, '$link must be a valid CID');
        }
    }
}
