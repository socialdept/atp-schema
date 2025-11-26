<?php

namespace SocialDept\AtpSchema\Data\Types;

use SocialDept\AtpSchema\Data\TypeDefinition;
use SocialDept\AtpSchema\Exceptions\RecordValidationException;

class UnionType extends TypeDefinition
{
    /**
     * Possible types (refs).
     *
     * @var array<string>
     */
    public readonly array $refs;

    /**
     * Whether this is a closed union (only listed refs allowed).
     */
    public readonly bool $closed;

    /**
     * Create a new UnionType.
     *
     * @param  array<string>  $refs
     */
    public function __construct(
        array $refs = [],
        bool $closed = false,
        ?string $description = null
    ) {
        parent::__construct('union', $description);

        $this->refs = $refs;
        $this->closed = $closed;
    }

    /**
     * Create from array data.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            refs: $data['refs'] ?? [],
            closed: $data['closed'] ?? false,
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

        if (! empty($this->refs)) {
            $array['refs'] = $this->refs;
        }

        if ($this->closed) {
            $array['closed'] = $this->closed;
        }

        return $array;
    }

    /**
     * Validate a value against this type definition.
     */
    public function validate(mixed $value, string $path = ''): void
    {
        if (! is_array($value)) {
            throw RecordValidationException::invalidType($path, 'union (object with $type)', gettype($value));
        }

        // Union types must have a $type discriminator
        if (! isset($value['$type'])) {
            throw RecordValidationException::invalidValue($path, 'must contain $type property');
        }

        $typeRef = $value['$type'];

        if (! is_string($typeRef)) {
            throw RecordValidationException::invalidValue($path, '$type must be a string');
        }

        // If closed, validate the type is in refs
        if ($this->closed && ! in_array($typeRef, $this->refs, true)) {
            $allowed = implode(', ', $this->refs);

            throw RecordValidationException::invalidValue($path, "type must be one of: {$allowed}");
        }

        // Note: Actual validation of the referenced type would happen
        // in a higher-level validator that has access to the schema repository
    }
}
