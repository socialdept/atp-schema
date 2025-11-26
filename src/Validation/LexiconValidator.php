<?php

namespace SocialDept\AtpSchema\Validation;

use SocialDept\AtpSchema\Contracts\LexiconValidator as LexiconValidatorContract;
use SocialDept\AtpSchema\Data\LexiconDocument;
use SocialDept\AtpSchema\Exceptions\RecordValidationException;
use SocialDept\AtpSchema\Exceptions\SchemaValidationException;
use SocialDept\AtpSchema\Parser\SchemaLoader;
use SocialDept\AtpSchema\Parser\TypeParser;

class LexiconValidator implements LexiconValidatorContract
{
    /**
     * Schema loader for loading lexicon documents.
     */
    protected SchemaLoader $schemaLoader;

    /**
     * Type parser for parsing and resolving types.
     */
    protected TypeParser $typeParser;

    /**
     * Validation mode.
     */
    protected string $mode = 'strict';

    /**
     * Create a new LexiconValidator.
     */
    public function __construct(
        SchemaLoader $schemaLoader,
        ?TypeParser $typeParser = null
    ) {
        $this->schemaLoader = $schemaLoader;
        $this->typeParser = $typeParser ?? new TypeParser(schemaLoader: $schemaLoader);
    }

    /**
     * Validate data against Lexicon schema.
     */
    public function validate(array $data, LexiconDocument $schema): bool
    {
        try {
            $this->validateRecord($schema, $data);

            return true;
        } catch (RecordValidationException|SchemaValidationException) {
            return false;
        }
    }

    /**
     * Validate and return errors.
     *
     * @return array<string, array<string>>
     */
    public function validateWithErrors(array $data, LexiconDocument $schema): array
    {
        try {
            $this->validateRecord($schema, $data);

            return [];
        } catch (RecordValidationException $e) {
            return ['record' => [$e->getMessage()]];
        } catch (SchemaValidationException $e) {
            return ['schema' => [$e->getMessage()]];
        }
    }

    /**
     * Validate a specific field.
     */
    public function validateField(mixed $value, string $field, LexiconDocument $schema): bool
    {
        try {
            $mainDef = $schema->getMainDefinition();

            if ($mainDef === null) {
                return false;
            }

            $recordSchema = $mainDef['record'] ?? null;

            if ($recordSchema === null || ! is_array($recordSchema)) {
                return false;
            }

            $properties = $recordSchema['properties'] ?? [];

            if (! isset($properties[$field])) {
                return false;
            }

            $fieldType = $this->typeParser->parse($properties[$field], $schema);
            $fieldType->validate($value, $field);

            return true;
        } catch (RecordValidationException) {
            return false;
        }
    }

    /**
     * Set validation mode (strict, optimistic, lenient).
     */
    public function setMode(string $mode): void
    {
        $this->mode = $mode;
    }

    /**
     * Validate a record by NSID string.
     */
    public function validateByNsid(string $nsid, array $record): void
    {
        $document = $this->schemaLoader->load($nsid);

        $this->validateRecord($document, $record);
    }

    /**
     * Validate a record against a lexicon document.
     */
    public function validateRecord(LexiconDocument $document, array $record): void
    {
        if (! $document->isRecord()) {
            throw SchemaValidationException::invalidStructure(
                $document->getNsid(),
                ['Schema is not a record type']
            );
        }

        $mainDef = $document->getMainDefinition();

        if ($mainDef === null) {
            throw SchemaValidationException::invalidStructure(
                $document->getNsid(),
                ['Missing main definition']
            );
        }

        // Get the record schema
        $recordSchema = $mainDef['record'] ?? null;

        if ($recordSchema === null || ! is_array($recordSchema)) {
            throw SchemaValidationException::invalidStructure(
                $document->getNsid(),
                ['Invalid record schema']
            );
        }

        // Parse and validate the record type
        $recordType = $this->typeParser->parse($recordSchema, $document);
        $recordType->validate($record, '$');
    }

    /**
     * Validate a query against its lexicon schema.
     */
    public function validateQuery(LexiconDocument $document, array $params): void
    {
        if (! $document->isQuery()) {
            throw SchemaValidationException::invalidStructure(
                $document->getNsid(),
                ['Schema is not a query type']
            );
        }

        $mainDef = $document->getMainDefinition();

        if ($mainDef === null) {
            throw SchemaValidationException::invalidStructure(
                $document->getNsid(),
                ['Missing main definition']
            );
        }

        // Get the parameters schema
        $paramsSchema = $mainDef['parameters'] ?? null;

        if ($paramsSchema !== null && is_array($paramsSchema)) {
            $paramsType = $this->typeParser->parse($paramsSchema, $document);
            $paramsType->validate($params, '$');
        }
    }

    /**
     * Validate a procedure against its lexicon schema.
     */
    public function validateProcedure(LexiconDocument $document, array $input): void
    {
        if (! $document->isProcedure()) {
            throw SchemaValidationException::invalidStructure(
                $document->getNsid(),
                ['Schema is not a procedure type']
            );
        }

        $mainDef = $document->getMainDefinition();

        if ($mainDef === null) {
            throw SchemaValidationException::invalidStructure(
                $document->getNsid(),
                ['Missing main definition']
            );
        }

        // Get the input schema
        $inputSchema = $mainDef['input'] ?? null;

        if ($inputSchema !== null && is_array($inputSchema)) {
            $inputType = $this->typeParser->parse($inputSchema, $document);
            $inputType->validate($input, '$');
        }
    }

    /**
     * Check if a record is valid.
     */
    public function isValid(string $nsid, array $record): bool
    {
        try {
            $this->validate($nsid, $record);

            return true;
        } catch (RecordValidationException) {
            return false;
        }
    }

    /**
     * Get validation errors for a record.
     *
     * @return array<string>
     */
    public function getErrors(string $nsid, array $record): array
    {
        try {
            $this->validate($nsid, $record);

            return [];
        } catch (RecordValidationException $e) {
            return [$e->getMessage()];
        }
    }
}
