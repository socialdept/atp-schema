<?php

namespace SocialDept\AtpSchema\Data;

abstract class TypeDefinition
{
    /**
     * Type identifier (string, number, object, array, etc).
     */
    public readonly string $type;

    /**
     * Optional description of this type.
     */
    public readonly ?string $description;

    /**
     * Create a new TypeDefinition.
     */
    public function __construct(
        string $type,
        ?string $description = null
    ) {
        $this->type = $type;
        $this->description = $description;
    }

    /**
     * Create type definition from array data.
     */
    abstract public static function fromArray(array $data): self;

    /**
     * Convert type definition to array.
     */
    abstract public function toArray(): array;

    /**
     * Validate a value against this type definition.
     *
     * @throws \SocialDept\AtpSchema\Exceptions\RecordValidationException
     */
    abstract public function validate(mixed $value, string $path = ''): void;

    /**
     * Check if this is a primitive type.
     */
    public function isPrimitive(): bool
    {
        return in_array($this->type, [
            'null',
            'boolean',
            'integer',
            'string',
            'bytes',
            'cid-link',
            'unknown',
        ]);
    }

    /**
     * Check if this is an object type.
     */
    public function isObject(): bool
    {
        return $this->type === 'object';
    }

    /**
     * Check if this is an array type.
     */
    public function isArray(): bool
    {
        return $this->type === 'array';
    }

    /**
     * Check if this is a union type.
     */
    public function isUnion(): bool
    {
        return $this->type === 'union';
    }

    /**
     * Check if this is a ref type.
     */
    public function isRef(): bool
    {
        return $this->type === 'ref';
    }

    /**
     * Check if this is a blob type.
     */
    public function isBlob(): bool
    {
        return $this->type === 'blob';
    }

    /**
     * Get the type identifier.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the description.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }
}
