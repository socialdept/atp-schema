<?php

namespace SocialDept\Schema\Generator;

use SocialDept\Schema\Data\LexiconDocument;
use SocialDept\Schema\Exceptions\GenerationException;

class ClassGenerator
{
    /**
     * Naming converter instance.
     */
    protected NamingConverter $naming;

    /**
     * Type mapper instance.
     */
    protected TypeMapper $typeMapper;

    /**
     * Stub renderer instance.
     */
    protected StubRenderer $renderer;

    /**
     * Method generator instance.
     */
    protected MethodGenerator $methodGenerator;

    /**
     * DocBlock generator instance.
     */
    protected DocBlockGenerator $docBlockGenerator;

    /**
     * Create a new ClassGenerator.
     */
    public function __construct(
        ?NamingConverter $naming = null,
        ?TypeMapper $typeMapper = null,
        ?StubRenderer $renderer = null,
        ?MethodGenerator $methodGenerator = null,
        ?DocBlockGenerator $docBlockGenerator = null
    ) {
        $this->naming = $naming ?? new NamingConverter();
        $this->typeMapper = $typeMapper ?? new TypeMapper($this->naming);
        $this->renderer = $renderer ?? new StubRenderer();
        $this->methodGenerator = $methodGenerator ?? new MethodGenerator($this->naming, $this->typeMapper, $this->renderer);
        $this->docBlockGenerator = $docBlockGenerator ?? new DocBlockGenerator($this->typeMapper);
    }

    /**
     * Generate a complete PHP class from a lexicon document.
     */
    public function generate(LexiconDocument $document): string
    {
        $nsid = $document->getNsid();
        $mainDef = $document->getMainDefinition();

        if ($mainDef === null) {
            throw GenerationException::withContext('No main definition found', ['nsid' => $nsid]);
        }

        $type = $mainDef['type'] ?? null;

        if (! in_array($type, ['record', 'object'])) {
            throw GenerationException::withContext(
                'Can only generate classes for record and object types',
                ['nsid' => $nsid, 'type' => $type]
            );
        }

        // Get class components
        $namespace = $this->naming->nsidToNamespace($nsid);
        $className = $this->naming->toClassName($document->id->getName());
        $useStatements = $this->collectUseStatements($mainDef);
        $properties = $this->generateProperties($mainDef);
        $constructor = $this->generateConstructor($mainDef);
        $methods = $this->generateMethods($document);
        $docBlock = $this->generateClassDocBlock($document, $mainDef);

        // Render the class
        return $this->renderer->render('class', [
            'namespace' => $namespace,
            'imports' => $this->formatUseStatements($useStatements),
            'docBlock' => $docBlock,
            'className' => $className,
            'extends' => ' extends \\SocialDept\\Schema\\Data\\Data',
            'implements' => '',
            'properties' => $properties,
            'constructor' => $constructor,
            'methods' => $methods,
        ]);
    }

    /**
     * Generate class properties.
     *
     * @param  array<string, mixed>  $definition
     */
    protected function generateProperties(array $definition): string
    {
        $properties = $definition['properties'] ?? [];
        $required = $definition['required'] ?? [];

        if (empty($properties)) {
            return '';
        }

        $lines = [];

        foreach ($properties as $name => $propDef) {
            $isRequired = in_array($name, $required);
            $phpType = $this->typeMapper->toPhpType($propDef, ! $isRequired);
            $docType = $this->typeMapper->toPhpDocType($propDef, ! $isRequired);
            $description = $propDef['description'] ?? null;

            // Build property doc comment
            $docLines = ['    /**'];
            if ($description) {
                $docLines[] = '     * '.$description;
                $docLines[] = '     *';
            }
            $docLines[] = '     * @var '.$docType;
            $docLines[] = '     */';

            $lines[] = implode("\n", $docLines);
            $lines[] = '    public readonly '.$phpType.' $'.$name.';';
            $lines[] = '';
        }

        return rtrim(implode("\n", $lines));
    }

    /**
     * Generate class constructor.
     *
     * @param  array<string, mixed>  $definition
     */
    protected function generateConstructor(array $definition): string
    {
        $properties = $definition['properties'] ?? [];
        $required = $definition['required'] ?? [];

        if (empty($properties)) {
            return '';
        }

        $params = [];

        foreach ($properties as $name => $propDef) {
            $isRequired = in_array($name, $required);
            $phpType = $this->typeMapper->toPhpType($propDef, ! $isRequired);
            $default = ! $isRequired ? ' = null' : '';

            $params[] = '        public readonly '.$phpType.' $'.$name.$default.',';
        }

        // Remove trailing comma from last parameter
        if (! empty($params)) {
            $params[count($params) - 1] = rtrim($params[count($params) - 1], ',');
        }

        return "    public function __construct(\n".implode("\n", $params)."\n    ) {\n    }";
    }

    /**
     * Generate class methods.
     */
    protected function generateMethods(LexiconDocument $document): string
    {
        $methods = $this->methodGenerator->generateAll($document);

        return implode("\n\n", $methods);
    }

    /**
     * Generate class-level documentation block.
     *
     * @param  array<string, mixed>  $definition
     */
    protected function generateClassDocBlock(LexiconDocument $document, array $definition): string
    {
        return $this->docBlockGenerator->generateClassDocBlock($document, $definition);
    }

    /**
     * Collect all use statements needed for the class.
     *
     * @param  array<string, mixed>  $definition
     * @return array<string>
     */
    protected function collectUseStatements(array $definition): array
    {
        $uses = ['SocialDept\\Schema\\Data\\Data'];
        $properties = $definition['properties'] ?? [];

        foreach ($properties as $propDef) {
            $propUses = $this->typeMapper->getUseStatements($propDef);
            $uses = array_merge($uses, $propUses);

            // Handle array items
            if (isset($propDef['items'])) {
                $itemUses = $this->typeMapper->getUseStatements($propDef['items']);
                $uses = array_merge($uses, $itemUses);
            }
        }

        // Remove duplicates and sort
        $uses = array_unique($uses);
        sort($uses);

        return $uses;
    }

    /**
     * Format use statements for output.
     *
     * @param  array<string>  $uses
     */
    protected function formatUseStatements(array $uses): string
    {
        if (empty($uses)) {
            return '';
        }

        $lines = [];
        foreach ($uses as $use) {
            $lines[] = 'use '.ltrim($use, '\\').';';
        }

        return implode("\n", $lines);
    }

    /**
     * Get the naming converter.
     */
    public function getNaming(): NamingConverter
    {
        return $this->naming;
    }

    /**
     * Get the type mapper.
     */
    public function getTypeMapper(): TypeMapper
    {
        return $this->typeMapper;
    }

    /**
     * Get the stub renderer.
     */
    public function getRenderer(): StubRenderer
    {
        return $this->renderer;
    }

    /**
     * Get the method generator.
     */
    public function getMethodGenerator(): MethodGenerator
    {
        return $this->methodGenerator;
    }

    /**
     * Get the docblock generator.
     */
    public function getDocBlockGenerator(): DocBlockGenerator
    {
        return $this->docBlockGenerator;
    }
}
