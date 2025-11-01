<?php

namespace SocialDept\Schema\Data;

use SocialDept\Schema\Exceptions\SchemaValidationException;
use SocialDept\Schema\Parser\Nsid;

class LexiconDocument
{
    /**
     * Lexicon version.
     */
    public readonly int $lexicon;

    /**
     * NSID identifier.
     */
    public readonly Nsid $id;

    /**
     * Schema description.
     */
    public readonly ?string $description;

    /**
     * Schema definitions.
     *
     * @var array<string, array>
     */
    public readonly array $defs;

    /**
     * Raw schema data.
     */
    public readonly array $raw;

    /**
     * Source of the schema (file path, URL, etc).
     */
    public readonly ?string $source;

    /**
     * Create a new LexiconDocument.
     *
     * @param  array<string, array>  $defs
     */
    public function __construct(
        int $lexicon,
        Nsid $id,
        array $defs,
        ?string $description = null,
        ?string $source = null,
        ?array $raw = null
    ) {
        $this->lexicon = $lexicon;
        $this->id = $id;
        $this->defs = $defs;
        $this->description = $description;
        $this->source = $source;
        $this->raw = $raw ?? [];
    }

    /**
     * Create from array data.
     */
    public static function fromArray(array $data, ?string $source = null): self
    {
        if (! isset($data['lexicon'])) {
            throw SchemaValidationException::missingField('unknown', 'lexicon');
        }

        if (! isset($data['id'])) {
            throw SchemaValidationException::missingField('unknown', 'id');
        }

        if (! isset($data['defs'])) {
            throw SchemaValidationException::missingField($data['id'], 'defs');
        }

        $lexicon = (int) $data['lexicon'];
        if ($lexicon !== 1) {
            throw SchemaValidationException::invalidVersion($data['id'], $lexicon);
        }

        return new self(
            lexicon: $lexicon,
            id: Nsid::parse($data['id']),
            defs: $data['defs'],
            description: $data['description'] ?? null,
            source: $source,
            raw: $data
        );
    }

    /**
     * Get a definition by name.
     */
    public function getDefinition(string $name): ?array
    {
        return $this->defs[$name] ?? null;
    }

    /**
     * Check if definition exists.
     */
    public function hasDefinition(string $name): bool
    {
        return isset($this->defs[$name]);
    }

    /**
     * Get the main definition.
     */
    public function getMainDefinition(): ?array
    {
        return $this->getDefinition('main');
    }

    /**
     * Get all definition names.
     *
     * @return array<string>
     */
    public function getDefinitionNames(): array
    {
        return array_keys($this->defs);
    }

    /**
     * Get NSID as string.
     */
    public function getNsid(): string
    {
        return $this->id->toString();
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'lexicon' => $this->lexicon,
            'id' => $this->id->toString(),
            'description' => $this->description,
            'defs' => $this->defs,
        ];
    }

    /**
     * Check if this is a record schema.
     */
    public function isRecord(): bool
    {
        $main = $this->getMainDefinition();

        return $main !== null && ($main['type'] ?? null) === 'record';
    }

    /**
     * Check if this is a query schema.
     */
    public function isQuery(): bool
    {
        $main = $this->getMainDefinition();

        return $main !== null && ($main['type'] ?? null) === 'query';
    }

    /**
     * Check if this is a procedure schema.
     */
    public function isProcedure(): bool
    {
        $main = $this->getMainDefinition();

        return $main !== null && ($main['type'] ?? null) === 'procedure';
    }

    /**
     * Check if this is a subscription schema.
     */
    public function isSubscription(): bool
    {
        $main = $this->getMainDefinition();

        return $main !== null && ($main['type'] ?? null) === 'subscription';
    }
}
