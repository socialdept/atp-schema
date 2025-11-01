<?php

namespace SocialDept\Schema;

use SocialDept\Schema\Data\LexiconDocument;
use SocialDept\Schema\Generator\DTOGenerator;
use SocialDept\Schema\Parser\SchemaLoader;
use SocialDept\Schema\Validation\LexiconValidator;

class SchemaManager
{
    /**
     * Schema loader instance.
     */
    protected SchemaLoader $loader;

    /**
     * Lexicon validator instance.
     */
    protected LexiconValidator $validator;

    /**
     * DTO generator instance.
     */
    protected ?DTOGenerator $generator = null;

    /**
     * Create a new SchemaManager.
     */
    public function __construct(
        SchemaLoader $loader,
        LexiconValidator $validator,
        ?DTOGenerator $generator = null
    ) {
        $this->loader = $loader;
        $this->validator = $validator;
        $this->generator = $generator;
    }

    /**
     * Load a schema by NSID.
     */
    public function load(string $nsid): array
    {
        return $this->loader->load($nsid);
    }

    /**
     * Check if a schema exists.
     */
    public function exists(string $nsid): bool
    {
        return $this->loader->exists($nsid);
    }

    /**
     * Parse a schema into a LexiconDocument.
     */
    public function parse(string $nsid): LexiconDocument
    {
        $schema = $this->loader->load($nsid);

        return LexiconDocument::fromArray($schema);
    }

    /**
     * Validate data against a schema.
     */
    public function validate(string $nsid, array $data): bool
    {
        $document = $this->parse($nsid);

        return $this->validator->validate($data, $document);
    }

    /**
     * Validate data and return errors.
     *
     * @return array<string, array<string>>
     */
    public function validateWithErrors(string $nsid, array $data): array
    {
        $document = $this->parse($nsid);

        return $this->validator->validateWithErrors($data, $document);
    }

    /**
     * Generate DTO code from a schema.
     */
    public function generate(string $nsid, array $options = []): string
    {
        if ($this->generator === null) {
            throw new \RuntimeException('Generator not available');
        }

        $document = $this->parse($nsid);

        return $this->generator->generate($document);
    }

    /**
     * Clear schema cache.
     */
    public function clearCache(?string $nsid = null): void
    {
        $this->loader->clearCache($nsid);
    }

    /**
     * Get the schema loader instance.
     */
    public function getLoader(): SchemaLoader
    {
        return $this->loader;
    }

    /**
     * Get the validator instance.
     */
    public function getValidator(): LexiconValidator
    {
        return $this->validator;
    }

    /**
     * Get the generator instance.
     */
    public function getGenerator(): ?DTOGenerator
    {
        return $this->generator;
    }

    /**
     * Set the generator instance.
     */
    public function setGenerator(DTOGenerator $generator): void
    {
        $this->generator = $generator;
    }
}
