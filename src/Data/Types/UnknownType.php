<?php

namespace SocialDept\AtpSchema\Data\Types;

use SocialDept\AtpSchema\Data\TypeDefinition;

class UnknownType extends TypeDefinition
{
    /**
     * Create a new UnknownType.
     */
    public function __construct(?string $description = null)
    {
        parent::__construct('unknown', $description);
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
        // Unknown type accepts any value
    }
}
