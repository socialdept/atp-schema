<?php

namespace SocialDept\Schema\Data\Types;

use SocialDept\Schema\Data\TypeDefinition;
use SocialDept\Schema\Exceptions\RecordValidationException;

class IntegerType extends TypeDefinition
{
    /**
     * Minimum value.
     */
    public readonly ?int $minimum;

    /**
     * Maximum value.
     */
    public readonly ?int $maximum;

    /**
     * Allowed enum values.
     *
     * @var array<int>|null
     */
    public readonly ?array $enum;

    /**
     * Constant value.
     */
    public readonly ?int $const;

    /**
     * Create a new IntegerType.
     *
     * @param  array<int>|null  $enum
     */
    public function __construct(
        ?string $description = null,
        ?int $minimum = null,
        ?int $maximum = null,
        ?array $enum = null,
        ?int $const = null
    ) {
        parent::__construct('integer', $description);

        $this->minimum = $minimum;
        $this->maximum = $maximum;
        $this->enum = $enum;
        $this->const = $const;
    }

    /**
     * Create from array data.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            description: $data['description'] ?? null,
            minimum: $data['minimum'] ?? null,
            maximum: $data['maximum'] ?? null,
            enum: $data['enum'] ?? null,
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

        if ($this->minimum !== null) {
            $array['minimum'] = $this->minimum;
        }

        if ($this->maximum !== null) {
            $array['maximum'] = $this->maximum;
        }

        if ($this->enum !== null) {
            $array['enum'] = $this->enum;
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
        if (! is_int($value)) {
            throw RecordValidationException::invalidType($path, 'integer', gettype($value));
        }

        // Const validation
        if ($this->const !== null && $value !== $this->const) {
            throw RecordValidationException::invalidValue($path, "must equal {$this->const}");
        }

        // Enum validation
        if ($this->enum !== null && ! in_array($value, $this->enum, true)) {
            throw RecordValidationException::invalidValue($path, 'must be one of: '.implode(', ', $this->enum));
        }

        // Range validation
        if ($this->minimum !== null && $value < $this->minimum) {
            throw RecordValidationException::invalidValue($path, "must be at least {$this->minimum}");
        }

        if ($this->maximum !== null && $value > $this->maximum) {
            throw RecordValidationException::invalidValue($path, "must be at most {$this->maximum}");
        }
    }
}
