<?php

namespace SocialDept\Schema\Parser;

use SocialDept\Schema\Data\LexiconDocument;
use SocialDept\Schema\Data\TypeDefinition;
use SocialDept\Schema\Exceptions\TypeResolutionException;

class TypeParser
{
    /**
     * Primitive type parser.
     */
    protected PrimitiveParser $primitiveParser;

    /**
     * Complex type parser.
     */
    protected ComplexTypeParser $complexParser;

    /**
     * Schema loader for resolving external references.
     */
    protected ?SchemaLoader $schemaLoader;

    /**
     * Cache of resolved types to prevent infinite loops.
     *
     * @var array<string, TypeDefinition>
     */
    protected array $resolvedTypes = [];

    /**
     * Current resolution chain to detect circular references.
     *
     * @var array<string>
     */
    protected array $resolutionChain = [];

    /**
     * Create a new TypeParser.
     */
    public function __construct(
        ?PrimitiveParser $primitiveParser = null,
        ?ComplexTypeParser $complexParser = null,
        ?SchemaLoader $schemaLoader = null
    ) {
        $this->primitiveParser = $primitiveParser ?? new PrimitiveParser;
        $this->complexParser = $complexParser ?? new ComplexTypeParser($this->primitiveParser);
        $this->schemaLoader = $schemaLoader;
    }

    /**
     * Parse a type definition from array data.
     *
     * @throws TypeResolutionException
     */
    public function parse(array $data, ?LexiconDocument $context = null): TypeDefinition
    {
        $type = $data['type'] ?? null;

        if ($type === null) {
            throw TypeResolutionException::unknownType('(missing type field)');
        }

        // Handle primitive types
        if ($this->primitiveParser->isPrimitive($type)) {
            return $this->primitiveParser->parse($data);
        }

        // Handle complex types
        if ($this->complexParser->isComplex($type)) {
            return $this->complexParser->parse($data);
        }

        throw TypeResolutionException::unknownType($type);
    }

    /**
     * Resolve a reference to its actual type definition.
     *
     * @throws TypeResolutionException
     */
    public function resolveReference(string $ref, LexiconDocument $context): TypeDefinition
    {
        // Check if already resolved
        $cacheKey = $context->getNsid().':'.$ref;

        if (isset($this->resolvedTypes[$cacheKey])) {
            return $this->resolvedTypes[$cacheKey];
        }

        // Check for circular reference
        if (in_array($cacheKey, $this->resolutionChain)) {
            throw TypeResolutionException::circularReference($ref, $this->resolutionChain);
        }

        $this->resolutionChain[] = $cacheKey;

        try {
            $type = $this->resolveReferenceInternal($ref, $context);
            $this->resolvedTypes[$cacheKey] = $type;

            return $type;
        } finally {
            array_pop($this->resolutionChain);
        }
    }

    /**
     * Internal reference resolution logic.
     *
     * @throws TypeResolutionException
     */
    protected function resolveReferenceInternal(string $ref, LexiconDocument $context): TypeDefinition
    {
        // Local reference (#defName)
        if (str_starts_with($ref, '#')) {
            $defName = substr($ref, 1);

            if (! $context->hasDefinition($defName)) {
                throw TypeResolutionException::unresolvableReference($ref, $context->getNsid());
            }

            $defData = $context->getDefinition($defName);

            return $this->parse($defData, $context);
        }

        // External reference (nsid#defName or just nsid for #main)
        if ($this->schemaLoader === null) {
            throw new \RuntimeException('Cannot resolve external reference without SchemaLoader');
        }

        [$nsid, $defName] = $this->parseExternalReference($ref);

        // Load external schema
        $externalSchema = $this->schemaLoader->load($nsid);
        $externalDoc = LexiconDocument::fromArray($externalSchema);

        // Get the definition
        if (! $externalDoc->hasDefinition($defName)) {
            throw TypeResolutionException::unresolvableReference($ref, $context->getNsid());
        }

        $defData = $externalDoc->getDefinition($defName);

        return $this->parse($defData, $externalDoc);
    }

    /**
     * Parse an external reference into NSID and definition name.
     *
     * @return array{0: string, 1: string}
     */
    protected function parseExternalReference(string $ref): array
    {
        if (str_contains($ref, '#')) {
            [$nsid, $defName] = explode('#', $ref, 2);

            return [$nsid, $defName];
        }

        // If no # is present, default to 'main'
        return [$ref, 'main'];
    }

    /**
     * Clear the resolution cache.
     */
    public function clearCache(): void
    {
        $this->resolvedTypes = [];
        $this->resolutionChain = [];
    }

    /**
     * Get the resolved types cache.
     *
     * @return array<string, TypeDefinition>
     */
    public function getResolvedTypes(): array
    {
        return $this->resolvedTypes;
    }
}
