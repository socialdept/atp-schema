<?php

namespace SocialDept\Schema\Data\Types;

use SocialDept\Schema\Data\TypeDefinition;
use SocialDept\Schema\Exceptions\RecordValidationException;

class ArrayType extends TypeDefinition
{
    /**
     * Type of array items.
     */
    public readonly ?TypeDefinition $items;

    /**
     * Minimum array length.
     */
    public readonly ?int $minLength;

    /**
     * Maximum array length.
     */
    public readonly ?int $maxLength;

    /**
     * Create a new ArrayType.
     */
    public function __construct(
        ?TypeDefinition $items = null,
        ?int $minLength = null,
        ?int $maxLength = null,
        ?string $description = null
    ) {
        parent::__construct('array', $description);

        $this->items = $items;
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
    }

    /**
     * Create from array data.
     */
    public static function fromArray(array $data): self
    {
        // Items will be parsed by TypeParser, this is just a placeholder
        return new self(
            items: null,
            minLength: $data['minLength'] ?? null,
            maxLength: $data['maxLength'] ?? null,
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

        if ($this->items !== null) {
            $array['items'] = $this->items->toArray();
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
        if (! is_array($value)) {
            throw RecordValidationException::invalidType($path, 'array', gettype($value));
        }

        // Check if it's a sequential array
        if (! array_is_list($value)) {
            throw RecordValidationException::invalidValue($path, 'must be a sequential array');
        }

        $length = count($value);

        // Validate length
        if ($this->minLength !== null && $length < $this->minLength) {
            throw RecordValidationException::invalidValue($path, "must have at least {$this->minLength} items");
        }

        if ($this->maxLength !== null && $length > $this->maxLength) {
            throw RecordValidationException::invalidValue($path, "must have at most {$this->maxLength} items");
        }

        // Validate items
        if ($this->items !== null) {
            foreach ($value as $index => $item) {
                $itemPath = "{$path}[{$index}]";
                $this->items->validate($item, $itemPath);
            }
        }
    }

    /**
     * Set items type after construction.
     */
    public function withItems(TypeDefinition $items): self
    {
        return new self(
            items: $items,
            minLength: $this->minLength,
            maxLength: $this->maxLength,
            description: $this->description
        );
    }
}
