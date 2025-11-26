<?php

namespace SocialDept\AtpSchema\Data\Types;

use SocialDept\AtpSchema\Data\TypeDefinition;
use SocialDept\AtpSchema\Exceptions\RecordValidationException;

class BooleanType extends TypeDefinition
{
    /**
     * Constant value.
     */
    public readonly ?bool $const;

    /**
     * Create a new BooleanType.
     */
    public function __construct(
        ?string $description = null,
        ?bool $const = null
    ) {
        parent::__construct('boolean', $description);

        $this->const = $const;
    }

    /**
     * Create from array data.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            description: $data['description'] ?? null,
            const: $data['const'] ?? null
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

        if ($this->const !== null) {
            $array['const'] = $this->const;
        }

        return $array;
    }

    /**
     * Validate a value against this type definition.
     */
    public function validate(mixed $value, string $path = ''): void
    {
        if (! is_bool($value)) {
            throw RecordValidationException::invalidType($path, 'boolean', gettype($value));
        }

        // Const validation
        if ($this->const !== null && $value !== $this->const) {
            $expected = $this->const ? 'true' : 'false';

            throw RecordValidationException::invalidValue($path, "must equal {$expected}");
        }
    }
}
