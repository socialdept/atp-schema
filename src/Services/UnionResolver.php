<?php

namespace SocialDept\Schema\Services;

use Illuminate\Support\Traits\Macroable;
use SocialDept\Schema\Contracts\LexiconRegistry;
use SocialDept\Schema\Data\LexiconDocument;
use SocialDept\Schema\Exceptions\RecordValidationException;

class UnionResolver
{
    use Macroable;

    /**
     * Create a new UnionResolver.
     */
    public function __construct(
        protected ?LexiconRegistry $registry = null
    ) {}

    /**
     * Resolve union type from data.
     *
     * Returns the NSID of the matched type for discriminated unions,
     * or null for open unions.
     */
    public function resolve(mixed $data, array $unionDef): ?string
    {
        // Check if this is a closed/discriminated union
        $closed = $unionDef['closed'] ?? false;

        if ($closed) {
            return $this->resolveDiscriminated($data, $unionDef);
        }

        return null;
    }

    /**
     * Resolve discriminated union type.
     */
    protected function resolveDiscriminated(mixed $data, array $unionDef): string
    {
        if (! is_array($data)) {
            throw RecordValidationException::invalidType('union', 'object', gettype($data));
        }

        if (! isset($data['$type'])) {
            throw RecordValidationException::invalidValue('union', 'Missing required $type field');
        }

        $type = $data['$type'];
        $refs = $unionDef['refs'] ?? [];

        if (! in_array($type, $refs, true)) {
            throw RecordValidationException::invalidValue(
                'union',
                "Type '{$type}' not in union. Allowed: ".implode(', ', $refs)
            );
        }

        return $type;
    }

    /**
     * Check if data matches a specific union type.
     */
    public function matches(mixed $data, string $expectedType, array $unionDef): bool
    {
        try {
            $resolvedType = $this->resolve($data, $unionDef);

            if ($resolvedType === null) {
                // Open union - can't determine type
                return false;
            }

            return $resolvedType === $expectedType;
        } catch (RecordValidationException) {
            return false;
        }
    }

    /**
     * Get the definition for the resolved type.
     */
    public function getTypeDefinition(mixed $data, array $unionDef): ?LexiconDocument
    {
        if ($this->registry === null) {
            return null;
        }

        $type = $this->resolve($data, $unionDef);

        if ($type === null) {
            return null;
        }

        return $this->registry->get($type);
    }

    /**
     * Validate that data is a valid discriminated union.
     */
    public function validateDiscriminated(mixed $data, array $refs): void
    {
        if (! is_array($data)) {
            throw RecordValidationException::invalidType('union', 'object', gettype($data));
        }

        if (! isset($data['$type'])) {
            throw RecordValidationException::invalidValue('union', 'Missing required $type field');
        }

        $type = $data['$type'];

        if (! in_array($type, $refs, true)) {
            throw RecordValidationException::invalidValue(
                'union',
                "Type '{$type}' not in union. Allowed: ".implode(', ', $refs)
            );
        }
    }

    /**
     * Extract type from discriminated union data.
     */
    public function extractType(mixed $data): ?string
    {
        if (! is_array($data)) {
            return null;
        }

        return $data['$type'] ?? null;
    }

    /**
     * Create discriminated union data.
     */
    public function createDiscriminated(string $type, array $data): array
    {
        return [...$data, '$type' => $type];
    }

    /**
     * Check if union definition is closed/discriminated.
     */
    public function isClosed(array $unionDef): bool
    {
        return $unionDef['closed'] ?? false;
    }

    /**
     * Get all possible types from union definition.
     *
     * @return array<string>
     */
    public function getTypes(array $unionDef): array
    {
        return $unionDef['refs'] ?? [];
    }

    /**
     * Set the lexicon registry.
     */
    public function setRegistry(LexiconRegistry $registry): self
    {
        $this->registry = $registry;

        return $this;
    }
}
