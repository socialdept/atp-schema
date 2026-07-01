<?php

namespace SocialDept\AtpSchema\Data;

use SocialDept\AtpSchema\Exceptions\SchemaValidationException;
use SocialDept\AtpSupport\Nsid;

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
     * Canonical lexicon type string for `$type` serialization. Normally the NSID,
     * but for a synthetic def document it is the fragment form `nsid#defName`
     * (the id itself must stay dotted so it can pass through Nsid::parse).
     */
    public readonly ?string $lexiconType;

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
        ?array $raw = null,
        ?string $lexiconType = null
    ) {
        $this->lexicon = $lexicon;
        $this->id = $id;
        $this->defs = $defs;
        $this->description = $description;
        $this->source = $source;
        $this->raw = $raw ?? [];
        $this->lexiconType = $lexiconType;
    }

    /**
     * Create from array data.
     */
    public static function fromArray(array $data, ?string $source = null, ?string $lexiconType = null): self
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
            raw: $data,
            lexiconType: $lexiconType
        );
    }

    /**
     * Create from JSON string.
     */
    public static function fromJson(string $json, ?string $source = null): self
    {
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON: '.json_last_error_msg());
        }

        if (! is_array($data)) {
            throw new \InvalidArgumentException('JSON must decode to an array');
        }

        return self::fromArray($data, $source);
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
     * Canonical lexicon type used for `$type` serialization: the NSID for a main
     * type, or the `nsid#defName` fragment for a def.
     */
    public function getLexiconType(): string
    {
        return $this->lexiconType ?? $this->getNsid();
    }

    /**
     * Get lexicon version.
     */
    public function getVersion(): int
    {
        return $this->lexicon;
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
