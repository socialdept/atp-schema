<?php

namespace SocialDept\AtpSchema\Generator;

use SocialDept\AtpSchema\Data\LexiconDocument;
use SocialDept\AtpSchema\Exceptions\GenerationException;
use SocialDept\AtpSchema\Support\ExtensionManager;

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
     * Extension manager instance.
     */
    protected ExtensionManager $extensions;

    /**
     * Create a new ClassGenerator.
     */
    public function __construct(
        ?NamingConverter $naming = null,
        ?TypeMapper $typeMapper = null,
        ?StubRenderer $renderer = null,
        ?MethodGenerator $methodGenerator = null,
        ?DocBlockGenerator $docBlockGenerator = null,
        ?ExtensionManager $extensions = null
    ) {
        $this->naming = $naming ?? new NamingConverter();
        $this->typeMapper = $typeMapper ?? new TypeMapper($this->naming);
        $this->renderer = $renderer ?? new StubRenderer();
        $this->methodGenerator = $methodGenerator ?? new MethodGenerator($this->naming, $this->typeMapper, $this->renderer);
        $this->docBlockGenerator = $docBlockGenerator ?? new DocBlockGenerator($this->typeMapper);
        $this->extensions = $extensions ?? new ExtensionManager();
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

        // For record types, extract the actual record definition
        $recordDef = $type === 'record' ? ($mainDef['record'] ?? []) : $mainDef;

        // Build local definition map for type resolution
        $localDefinitions = $this->buildLocalDefinitionMap($document);
        $this->typeMapper->setLocalDefinitions($localDefinitions);

        // Get class components
        $namespace = $this->extensions->filter('filter:class:namespace', $this->naming->nsidToNamespace($nsid), $document);
        $className = $this->extensions->filter('filter:class:className', $this->naming->toClassName($document->id->getName()), $document);
        $useStatements = $this->extensions->filter('filter:class:useStatements', $this->collectUseStatements($recordDef, $namespace, $className), $document, $recordDef);
        $properties = $this->extensions->filter('filter:class:properties', $this->generateProperties($recordDef), $document, $recordDef);
        $constructor = $this->extensions->filter('filter:class:constructor', $this->generateConstructor($recordDef), $document, $recordDef);
        $methods = $this->extensions->filter('filter:class:methods', $this->generateMethods($document), $document);
        $docBlock = $this->extensions->filter('filter:class:docBlock', $this->generateClassDocBlock($document, $mainDef), $document, $mainDef);

        // Render the class
        $rendered = $this->renderer->render('class', [
            'namespace' => $namespace,
            'imports' => $this->formatUseStatements($useStatements),
            'docBlock' => $docBlock,
            'className' => $className,
            'extends' => ' extends Data',
            'implements' => '',
            'properties' => $properties,
            'constructor' => $constructor,
            'methods' => $methods,
        ]);

        // Fix blank lines when there's no constructor or properties
        if (empty($properties) && empty($constructor)) {
            // Remove double blank lines after class opening brace
            $rendered = preg_replace('/\{\n\n\n/', "{\n", $rendered);
        }

        // Execute post-generation hooks
        $this->extensions->execute('action:class:generated', $rendered, $document);

        return $rendered;
    }

    /**
     * Generate class properties.
     *
     * Since we use constructor property promotion, we don't need separate property declarations.
     * This method returns empty string but is kept for compatibility.
     *
     * @param  array<string, mixed>  $definition
     */
    protected function generateProperties(array $definition): string
    {
        // Properties are defined via constructor promotion
        return '';
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

        // Build constructor parameters - required first, then optional
        $requiredParams = [];
        $optionalParams = [];
        $requiredDocParams = [];
        $optionalDocParams = [];

        foreach ($properties as $name => $propDef) {
            $isRequired = in_array($name, $required);
            $phpType = $this->typeMapper->toPhpType($propDef, ! $isRequired);
            $phpDocType = $this->typeMapper->toPhpDocType($propDef, ! $isRequired);
            $description = $propDef['description'] ?? '';
            $param = '        public readonly '.$phpType.' $'.$name;

            if ($isRequired) {
                $requiredParams[] = $param.',';
                if ($description) {
                    $requiredDocParams[] = '     * @param  '.$phpDocType.'  $'.$name.'  '.$description;
                }
            } else {
                $optionalParams[] = $param.' = null,';
                if ($description) {
                    $optionalDocParams[] = '     * @param  '.$phpDocType.'  $'.$name.'  '.$description;
                }
            }
        }

        // Combine required and optional parameters
        $params = array_merge($requiredParams, $optionalParams);

        // Remove trailing comma from last parameter
        if (! empty($params)) {
            $params[count($params) - 1] = rtrim($params[count($params) - 1], ',');
        }

        // Build constructor DocBlock with parameter descriptions in the correct order
        $docParams = array_merge($requiredDocParams, $optionalDocParams);

        // Only add docblock if there are parameter descriptions
        if (! empty($docParams)) {
            $docLines = ['    /**'];
            $docLines = array_merge($docLines, $docParams);
            $docLines[] = '     */';
            $docBlock = implode("\n", $docLines)."\n";
        } else {
            $docBlock = '';
        }

        return $docBlock."    public function __construct(\n".implode("\n", $params)."\n    ) {\n    }";
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
    protected function collectUseStatements(array $definition, string $currentNamespace = '', string $currentClassName = ''): array
    {
        $uses = ['SocialDept\\AtpSchema\\Data\\Data'];
        $properties = $definition['properties'] ?? [];
        $hasUnions = false;
        $localRefs = [];

        foreach ($properties as $propDef) {
            $propUses = $this->typeMapper->getUseStatements($propDef);
            $uses = array_merge($uses, $propUses);

            // Check if this property uses unions
            if (isset($propDef['type']) && $propDef['type'] === 'union') {
                $hasUnions = true;
            }

            // Collect local references for import
            if (isset($propDef['type']) && $propDef['type'] === 'ref' && isset($propDef['ref'])) {
                $ref = $propDef['ref'];
                if (str_starts_with($ref, '#')) {
                    $localRefs[] = ltrim($ref, '#');
                }
            }

            // Handle array items
            if (isset($propDef['items'])) {
                $itemUses = $this->typeMapper->getUseStatements($propDef['items']);
                $uses = array_merge($uses, $itemUses);

                // Check for local refs in array items
                if (isset($propDef['items']['type']) && $propDef['items']['type'] === 'ref' && isset($propDef['items']['ref'])) {
                    $ref = $propDef['items']['ref'];
                    if (str_starts_with($ref, '#')) {
                        $localRefs[] = ltrim($ref, '#');
                    }
                }
            }
        }

        // Add local ref imports
        // For local refs, check if they should be nested or siblings
        if (! empty($localRefs) && $currentNamespace) {
            foreach ($localRefs as $localRef) {
                $refClassName = $this->naming->toClassName($localRef);

                // If this is a nested definition (has currentClassName) and it's a record type,
                // then local refs are nested under the record
                if ($currentClassName && $definition['type'] === 'record') {
                    $uses[] = $currentNamespace . '\\' . $currentClassName . '\\' . $refClassName;
                } else {
                    // For object definitions or defs lexicons, local refs are siblings
                    $uses[] = $currentNamespace . '\\' . $refClassName;
                }
            }
        }

        // Add UnionHelper if unions are used
        if ($hasUnions) {
            $uses[] = 'SocialDept\\AtpSchema\\Support\\UnionHelper';
        }

        // Remove duplicates and sort
        $uses = array_unique($uses);

        // Filter out classes from the same namespace
        if ($currentNamespace) {
            $uses = array_filter($uses, function ($use) use ($currentNamespace) {
                // Get namespace from FQCN by removing class name
                $parts = explode('\\', ltrim($use, '\\'));
                array_pop($parts); // Remove class name
                $useNamespace = implode('\\', $parts);

                return $useNamespace !== $currentNamespace;
            });
        }

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
     * Build a map of local definitions for type resolution.
     *
     * Maps local references (#defName) to their generated class names.
     *
     * @return array<string, string> Map of local ref => class name
     */
    protected function buildLocalDefinitionMap(LexiconDocument $document): array
    {
        $localDefs = [];
        $allDefs = $document->defs ?? [];

        foreach ($allDefs as $defName => $definition) {
            // Skip the main definition
            if ($defName === 'main') {
                continue;
            }

            // Convert definition name to class name
            $className = $this->naming->toClassName($defName);
            $localDefs["#{$defName}"] = $className;
        }

        return $localDefs;
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

    /**
     * Get the extension manager.
     */
    public function getExtensions(): ExtensionManager
    {
        return $this->extensions;
    }
}
