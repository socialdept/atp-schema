<?php

namespace SocialDept\Schema\Data\Types;

use SocialDept\Schema\Data\TypeDefinition;

class RefType extends TypeDefinition
{
    /**
     * Reference to another type (NSID or local #def).
     */
    public readonly string $ref;

    /**
     * Create a new RefType.
     */
    public function __construct(
        string $ref,
        ?string $description = null
    ) {
        parent::__construct('ref', $description);

        $this->ref = $ref;
    }

    /**
     * Create from array data.
     */
    public static function fromArray(array $data): self
    {
        if (! isset($data['ref'])) {
            throw new \InvalidArgumentException('RefType requires a ref property');
        }

        return new self(
            ref: $data['ref'],
            description: $data['description'] ?? null
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        $array = [
            'type' => $this->type,
            'ref' => $this->ref,
        ];

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
        // Ref validation requires resolving the reference to its actual type
        // This would be handled by a higher-level validator with schema repository access
        // For now, we just accept any value
    }

    /**
     * Check if this is a local reference (starts with #).
     */
    public function isLocal(): bool
    {
        return str_starts_with($this->ref, '#');
    }

    /**
     * Check if this is an external reference (contains a dot).
     */
    public function isExternal(): bool
    {
        return str_contains($this->ref, '.') && ! $this->isLocal();
    }

    /**
     * Get the definition name from a local reference.
     */
    public function getLocalDefinition(): ?string
    {
        if (! $this->isLocal()) {
            return null;
        }

        return substr($this->ref, 1);
    }
}
