<?php

namespace SocialDept\Schema\Generator;

use SocialDept\Schema\Contracts\DataGenerator;
use SocialDept\Schema\Data\LexiconDocument;
use SocialDept\Schema\Parser\SchemaLoader;
use SocialDept\Schema\Parser\TypeParser;

class DTOGenerator implements DataGenerator
{
    /**
     * Schema loader for loading lexicon documents.
     */
    protected SchemaLoader $schemaLoader;

    /**
     * Type parser for parsing type definitions.
     */
    protected TypeParser $typeParser;

    /**
     * Namespace resolver for converting NSIDs to PHP namespaces.
     */
    protected NamespaceResolver $namespaceResolver;

    /**
     * Template renderer for generating PHP code.
     */
    protected TemplateRenderer $templateRenderer;

    /**
     * File writer for writing generated files.
     */
    protected FileWriter $fileWriter;

    /**
     * Class generator for generating PHP classes.
     */
    protected ClassGenerator $classGenerator;

    /**
     * Base namespace for generated classes.
     */
    protected string $baseNamespace;

    /**
     * Output directory for generated files.
     */
    protected string $outputDirectory;

    /**
     * Create a new DTOGenerator.
     */
    public function __construct(
        SchemaLoader $schemaLoader,
        string $baseNamespace = 'App\\Lexicons',
        string $outputDirectory = 'app/Lexicons',
        ?TypeParser $typeParser = null,
        ?NamespaceResolver $namespaceResolver = null,
        ?TemplateRenderer $templateRenderer = null,
        ?FileWriter $fileWriter = null,
        ?ClassGenerator $classGenerator = null
    ) {
        $this->schemaLoader = $schemaLoader;
        $this->baseNamespace = rtrim($baseNamespace, '\\');
        $this->outputDirectory = rtrim($outputDirectory, '/');
        $this->typeParser = $typeParser ?? new TypeParser(schemaLoader: $schemaLoader);
        $this->namespaceResolver = $namespaceResolver ?? new NamespaceResolver($baseNamespace);
        $this->templateRenderer = $templateRenderer ?? new TemplateRenderer;
        $this->fileWriter = $fileWriter ?? new FileWriter;

        // Initialize ClassGenerator with proper naming converter
        $naming = new NamingConverter($this->baseNamespace);
        $this->classGenerator = $classGenerator ?? new ClassGenerator($naming);
    }

    /**
     * Generate PHP class files from Lexicon definition.
     */
    public function generate(LexiconDocument $schema): string
    {
        return $this->classGenerator->generate($schema);
    }

    /**
     * Generate and write class file to disk.
     */
    public function generateAndSave(LexiconDocument $schema, string $outputPath): string
    {
        $code = $this->generate($schema);
        $this->fileWriter->write($outputPath, $code);

        return $outputPath;
    }

    /**
     * Generate class content without writing to disk.
     */
    public function preview(LexiconDocument $schema): string
    {
        return $this->generate($schema);
    }

    /**
     * Set the base namespace for generated classes.
     */
    public function setBaseNamespace(string $namespace): void
    {
        $this->baseNamespace = rtrim($namespace, '\\');
        $this->namespaceResolver = new NamespaceResolver($this->baseNamespace);
    }

    /**
     * Set the output path for generated classes.
     */
    public function setOutputPath(string $path): void
    {
        $this->outputDirectory = rtrim($path, '/');
    }

    /**
     * Generate DTO classes from NSID.
     */
    public function generateByNsid(string $nsid, array $options = []): array
    {
        $document = $this->schemaLoader->load($nsid);

        return $this->generateFromDocument($document, $options);
    }

    /**
     * Generate DTO classes from a lexicon document.
     */
    public function generateFromDocument(LexiconDocument $document, array $options = []): array
    {
        $generatedFiles = [];

        // Generate main class if it's a record
        if ($document->isRecord()) {
            $file = $this->generateRecordClass($document, $options);
            $generatedFiles[] = $file;
        }

        // Generate classes for other definitions
        foreach ($document->getDefinitionNames() as $defName) {
            if ($defName === 'main') {
                continue;
            }

            $definition = $document->getDefinition($defName);

            if (isset($definition['type']) && $definition['type'] === 'object') {
                $file = $this->generateDefinitionClass($document, $defName, $options);
                $generatedFiles[] = $file;
            }
        }

        return $generatedFiles;
    }

    /**
     * Generate code for a record (without writing to disk).
     */
    protected function generateRecordCode(LexiconDocument $document): string
    {
        $namespace = $this->namespaceResolver->resolveNamespace($document->getNsid());
        $className = $this->namespaceResolver->resolveClassName($document->getNsid());

        $mainDef = $document->getMainDefinition();
        $recordSchema = $mainDef['record'] ?? [];

        $properties = $this->extractProperties($recordSchema, $document);

        return $this->templateRenderer->render('record', [
            'namespace' => $namespace,
            'className' => $className,
            'nsid' => $document->getNsid(),
            'description' => $document->description,
            'properties' => $properties,
        ]);
    }

    /**
     * Generate a record class from a lexicon document.
     */
    protected function generateRecordClass(LexiconDocument $document, array $options = []): string
    {
        // Use ClassGenerator for proper code generation
        $code = $this->classGenerator->generate($document);

        $naming = $this->classGenerator->getNaming();
        $namespace = $naming->nsidToNamespace($document->getNsid());
        $className = $naming->toClassName($document->id->getName());
        $filePath = $this->getFilePath($namespace, $className);

        if (! ($options['dryRun'] ?? false)) {
            $this->fileWriter->write($filePath, $code);
        }

        return $filePath;
    }

    /**
     * Generate a class for a specific definition.
     */
    protected function generateDefinitionClass(LexiconDocument $document, string $defName, array $options = []): string
    {
        // Create a temporary document for this specific definition
        $definition = $document->getDefinition($defName);

        // Build a temporary lexicon document for the object definition
        $objectNsid = $document->getNsid().'.'.$defName;
        $tempSchema = [
            'id' => $objectNsid,
            'lexicon' => 1,
            'description' => $definition['description'] ?? null,
            'defs' => [
                'main' => [
                    'type' => 'object',
                    'properties' => $definition['properties'] ?? [],
                    'required' => $definition['required'] ?? [],
                    'description' => $definition['description'] ?? null,
                ],
            ],
        ];

        $tempDocument = \SocialDept\Schema\Data\LexiconDocument::fromArray($tempSchema);

        // Use ClassGenerator for proper code generation
        $code = $this->classGenerator->generate($tempDocument);

        $naming = $this->classGenerator->getNaming();
        $namespace = $naming->nsidToNamespace($objectNsid);
        $className = $naming->toClassName($defName);
        $filePath = $this->getFilePath($namespace, $className);

        if (! ($options['dryRun'] ?? false)) {
            $this->fileWriter->write($filePath, $code);
        }

        return $filePath;
    }

    /**
     * Extract properties from a schema definition.
     *
     * @return array<array{name: string, type: string, phpType: string, required: bool, description: ?string}>
     */
    protected function extractProperties(array $schema, LexiconDocument $document): array
    {
        $properties = [];
        $schemaProperties = $schema['properties'] ?? [];
        $required = $schema['required'] ?? [];

        foreach ($schemaProperties as $name => $propSchema) {
            $properties[] = [
                'name' => $name,
                'type' => $propSchema['type'] ?? 'unknown',
                'phpType' => $this->mapToPhpType($propSchema),
                'required' => in_array($name, $required),
                'description' => $propSchema['description'] ?? null,
            ];
        }

        return $properties;
    }

    /**
     * Map lexicon type to PHP type.
     */
    protected function mapToPhpType(array $typeSchema): string
    {
        $type = $typeSchema['type'] ?? 'unknown';

        return match ($type) {
            'null' => 'null',
            'boolean' => 'bool',
            'integer' => 'int',
            'string' => 'string',
            'bytes' => 'string',
            'array' => 'array',
            'object' => 'array',
            'unknown' => 'mixed',
            default => 'mixed',
        };
    }

    /**
     * Get the file path for a generated class.
     */
    protected function getFilePath(string $namespace, string $className): string
    {
        // Remove base namespace from full namespace
        $relativePath = str_replace($this->baseNamespace.'\\', '', $namespace);
        $relativePath = str_replace('\\', '/', $relativePath);

        return $this->outputDirectory.'/'.$relativePath.'/'.$className.'.php';
    }

    /**
     * Validate generated code.
     */
    public function validate(string $code): bool
    {
        // Basic syntax check using token_get_all
        $tokens = @token_get_all($code);

        return $tokens !== false;
    }

    /**
     * Get generated file metadata.
     */
    public function getMetadata(string $nsid): array
    {
        $document = $this->schemaLoader->load($nsid);

        $namespace = $this->namespaceResolver->resolveNamespace($document->getNsid());
        $className = $this->namespaceResolver->resolveClassName($document->getNsid());

        return [
            'nsid' => $nsid,
            'namespace' => $namespace,
            'className' => $className,
            'fullyQualifiedName' => $namespace.'\\'.$className,
            'type' => $document->isRecord() ? 'record' : 'object',
        ];
    }

    /**
     * Set output options.
     */
    public function setOptions(array $options): void
    {
        if (isset($options['baseNamespace'])) {
            $this->baseNamespace = rtrim($options['baseNamespace'], '\\');
            $this->namespaceResolver = new NamespaceResolver($this->baseNamespace);
        }

        if (isset($options['outputDirectory'])) {
            $this->outputDirectory = rtrim($options['outputDirectory'], '/');
        }
    }
}
