<?php

namespace SocialDept\AtpSchema\Generator;

use SocialDept\AtpSchema\Data\LexiconDocument;
use SocialDept\AtpSchema\Support\ExtensionManager;

class MethodGenerator
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
     * Model mapper instance.
     */
    protected ModelMapper $modelMapper;

    /**
     * Extension manager instance.
     */
    protected ExtensionManager $extensions;

    /**
     * Create a new MethodGenerator.
     */
    public function __construct(
        ?NamingConverter $naming = null,
        ?TypeMapper $typeMapper = null,
        ?StubRenderer $renderer = null,
        ?ModelMapper $modelMapper = null,
        ?ExtensionManager $extensions = null
    ) {
        $this->naming = $naming ?? new NamingConverter();
        $this->typeMapper = $typeMapper ?? new TypeMapper($this->naming);
        $this->renderer = $renderer ?? new StubRenderer();
        $this->modelMapper = $modelMapper ?? new ModelMapper($this->naming, $this->typeMapper);
        $this->extensions = $extensions ?? new ExtensionManager();
    }

    /**
     * Generate all standard methods for a data class.
     *
     * @return array<string>
     */
    public function generateAll(LexiconDocument $document): array
    {
        return [
            $this->generateGetLexicon($document),
            $this->generateFromArray($document),
        ];
    }

    /**
     * Generate getLexicon method.
     */
    public function generateGetLexicon(LexiconDocument $document): string
    {
        $nsid = $document->getNsid();

        $method = $this->renderer->render('method', [
            'docBlock' => $this->generateDocBlock('Get the lexicon NSID for this data type.', 'string'),
            'visibility' => 'public ',
            'static' => 'static ',
            'name' => 'getLexicon',
            'parameters' => '',
            'returnType' => ': string',
            'body' => "        return '{$nsid}';",
        ]);

        return $this->extensions->filter('filter:method:getLexicon', $method, $document);
    }

    /**
     * Generate fromArray method.
     */
    public function generateFromArray(LexiconDocument $document): string
    {
        $mainDef = $document->getMainDefinition();

        // For record types, properties are nested under 'record'
        $type = $mainDef['type'] ?? null;
        if ($type === 'record') {
            $recordDef = $mainDef['record'] ?? [];
            $properties = $recordDef['properties'] ?? [];
            $required = $recordDef['required'] ?? [];
        } else {
            // For object types, properties are at the top level
            $properties = $mainDef['properties'] ?? [];
            $required = $mainDef['required'] ?? [];
        }

        if (empty($properties)) {
            return $this->generateEmptyFromArray();
        }

        $assignments = $this->generateFromArrayAssignments($properties, $required);
        $body = "        return new static(\n".$assignments."\n        );";

        $method = $this->renderer->render('method', [
            'docBlock' => $this->generateDocBlock('Create an instance from an array.', 'static', [
                ['name' => 'data', 'type' => 'array', 'description' => 'The data array'],
            ]),
            'visibility' => 'public ',
            'static' => 'static ',
            'name' => 'fromArray',
            'parameters' => 'array $data',
            'returnType' => ': static',
            'body' => $body,
        ]);

        return $this->extensions->filter('filter:method:fromArray', $method, $document, $properties, $required);
    }

    /**
     * Generate fromArray for empty properties.
     */
    protected function generateEmptyFromArray(): string
    {
        return $this->renderer->render('method', [
            'docBlock' => $this->generateDocBlock('Create an instance from an array.', 'static', [
                ['name' => 'data', 'type' => 'array', 'description' => 'The data array'],
            ]),
            'visibility' => 'public ',
            'static' => 'static ',
            'name' => 'fromArray',
            'parameters' => 'array $data',
            'returnType' => ': static',
            'body' => '        return new static();',
        ]);
    }

    /**
     * Generate assignments for fromArray method.
     *
     * @param  array<string, array<string, mixed>>  $properties
     * @param  array<string>  $required
     */
    protected function generateFromArrayAssignments(array $properties, array $required): string
    {
        $lines = [];

        // Generate required parameters first
        foreach ($properties as $name => $definition) {
            if (in_array($name, $required)) {
                $type = $definition['type'] ?? 'unknown';
                $assignment = $this->generatePropertyAssignment($name, $definition, $type, $required);
                $lines[] = '            '.$name.': '.$assignment.',';
            }
        }

        // Then generate optional parameters
        foreach ($properties as $name => $definition) {
            if (! in_array($name, $required)) {
                $type = $definition['type'] ?? 'unknown';
                $assignment = $this->generatePropertyAssignment($name, $definition, $type, $required);
                $lines[] = '            '.$name.': '.$assignment.',';
            }
        }

        // Remove trailing comma from last line
        if (! empty($lines)) {
            $lines[count($lines) - 1] = rtrim($lines[count($lines) - 1], ',');
        }

        return implode("\n", $lines);
    }

    /**
     * Generate assignment for a property in fromArray.
     *
     * @param  array<string, mixed>  $definition
     * @param  array<string>  $required
     */
    protected function generatePropertyAssignment(string $name, array $definition, string $type, array $required): string
    {
        $isRequired = in_array($name, $required);
        $assignment = $this->generatePropertyAssignmentInternal($name, $definition, $type, $required);

        return $this->extensions->filter('filter:method:propertyAssignment', $assignment, $name, $definition, $type, $required);
    }

    /**
     * Internal property assignment generation logic.
     *
     * @param  array<string, mixed>  $definition
     * @param  array<string>  $required
     */
    protected function generatePropertyAssignmentInternal(string $name, array $definition, string $type, array $required): string
    {
        $isRequired = in_array($name, $required);

        // Handle reference types
        if ($type === 'ref' && isset($definition['ref'])) {
            $ref = $definition['ref'];

            // Handle local references (starting with #) - use class from localDefinitions
            if (str_starts_with($ref, '#')) {
                $className = $this->naming->toClassName(ltrim($ref, '#'));

                if ($isRequired) {
                    return "{$className}::fromArray(\$data['{$name}'])";
                }

                return "isset(\$data['{$name}']) ? {$className}::fromArray(\$data['{$name}']) : null";
            }

            // Handle NSID fragments
            if (str_contains($ref, '#')) {
                [$baseNsid, $fragment] = explode('#', $ref, 2);
                $className = $this->naming->toClassName($fragment);
            } else {
                $refClass = $this->naming->nsidToClassName($ref);
                $className = basename(str_replace('\\', '/', $refClass));
            }

            if ($isRequired) {
                return "{$className}::fromArray(\$data['{$name}'])";
            }

            return "isset(\$data['{$name}']) ? {$className}::fromArray(\$data['{$name}']) : null";
        }

        // Handle arrays of references
        if ($type === 'array' && isset($definition['items']['type']) && $definition['items']['type'] === 'ref') {
            $ref = $definition['items']['ref'];

            // Handle local references - hydrate each item
            if (str_starts_with($ref, '#')) {
                $className = $this->naming->toClassName(ltrim($ref, '#'));

                return "isset(\$data['{$name}']) ? array_map(fn (\$item) => {$className}::fromArray(\$item), \$data['{$name}']) : []";
            }

            // Handle NSID fragments
            if (str_contains($ref, '#')) {
                [$baseNsid, $fragment] = explode('#', $ref, 2);
                $className = $this->naming->toClassName($fragment);
            } else {
                $refClass = $this->naming->nsidToClassName($ref);
                $className = basename(str_replace('\\', '/', $refClass));
            }

            return "isset(\$data['{$name}']) ? array_map(fn (\$item) => {$className}::fromArray(\$item), \$data['{$name}']) : []";
        }

        // Handle arrays of objects
        if ($type === 'array' && isset($definition['items']['type']) && $definition['items']['type'] === 'object') {
            return "\$data['{$name}'] ?? []";
        }

        // Handle DateTime types (if string format matches ISO8601)
        if ($type === 'string' && isset($definition['format']) && $definition['format'] === 'datetime') {
            if ($isRequired) {
                return "Carbon::parse(\$data['{$name}'])";
            }

            return "isset(\$data['{$name}']) ? Carbon::parse(\$data['{$name}']) : null";
        }

        // Handle union types with refs
        if ($type === 'union' && isset($definition['refs']) && is_array($definition['refs'])) {
            $refs = $definition['refs'];
            $isClosed = $definition['closed'] ?? false;

            // Filter out local references
            $externalRefs = array_values(array_filter($refs, fn ($ref) => ! str_starts_with($ref, '#')));

            // Handle closed unions - use UnionHelper for discrimination
            if ($isClosed && ! empty($externalRefs)) {
                // Build array of variant class names
                $variantClasses = [];
                foreach ($externalRefs as $ref) {
                    // Handle NSID fragments
                    if (str_contains($ref, '#')) {
                        [$baseNsid, $fragment] = explode('#', $ref, 2);
                        $className = $this->naming->toClassName($fragment);
                    } else {
                        $refClass = $this->naming->nsidToClassName($ref);
                        $className = basename(str_replace('\\', '/', $refClass));
                    }
                    $variantClasses[] = "{$className}::class";
                }

                $variantsArray = '['.implode(', ', $variantClasses).']';

                if ($isRequired) {
                    return "UnionHelper::resolveClosedUnion(\$data['{$name}'], {$variantsArray})";
                }

                return "isset(\$data['{$name}']) ? UnionHelper::resolveClosedUnion(\$data['{$name}'], {$variantsArray}) : null";
            }

            // Open unions - validate $type presence using UnionHelper
            if (! $isClosed) {
                if ($isRequired) {
                    return "UnionHelper::validateOpenUnion(\$data['{$name}'])";
                }

                return "isset(\$data['{$name}']) ? UnionHelper::validateOpenUnion(\$data['{$name}']) : null";
            }

            // Fallback for unions with only local refs
            if ($isRequired) {
                return "\$data['{$name}']";
            }

            return "\$data['{$name}'] ?? null";
        }

        // Handle blob types - hydrate as BlobReference
        if ($type === 'blob') {
            if ($isRequired) {
                return "BlobReference::fromArray(\$data['{$name}'])";
            }

            return "isset(\$data['{$name}']) ? BlobReference::fromArray(\$data['{$name}']) : null";
        }

        // Default: simple property access
        if ($isRequired) {
            return "\$data['{$name}']";
        }

        return "\$data['{$name}'] ?? null";
    }

    /**
     * Generate a generic method.
     *
     * @param  array<array{name: string, type: string, description?: string}>  $params
     */
    public function generate(
        string $name,
        string $returnType,
        string $body,
        ?string $description = null,
        array $params = [],
        bool $isStatic = false
    ): string {
        $parameters = $this->formatParameters($params);

        return $this->renderer->render('method', [
            'docBlock' => $this->generateDocBlock($description, $returnType, $params),
            'visibility' => 'public ',
            'static' => $isStatic ? 'static ' : '',
            'name' => $name,
            'parameters' => $parameters,
            'returnType' => $returnType ? ': '.$returnType : '',
            'body' => $body,
        ]);
    }

    /**
     * Generate method documentation block.
     *
     * @param  array<array{name: string, type: string, description?: string}>  $params
     */
    protected function generateDocBlock(?string $description, ?string $returnType, array $params = []): string
    {
        $lines = ['    /**'];

        if ($description) {
            $lines[] = '     * '.$description;

            if (! empty($params) || $returnType) {
                $lines[] = '     *';
            }
        }

        // Add parameter docs
        foreach ($params as $param) {
            $desc = $param['description'] ?? '';
            if ($desc) {
                $lines[] = '     * @param  '.$param['type'].'  $'.$param['name'].'  '.$desc;
            } else {
                $lines[] = '     * @param  '.$param['type'].'  $'.$param['name'];
            }
        }

        // Add return type
        if ($returnType && $returnType !== 'void') {
            $lines[] = '     * @return '.$returnType;
        }

        $lines[] = '     */';

        return implode("\n", $lines);
    }

    /**
     * Format method parameters.
     *
     * @param  array<array{name: string, type: string, description?: string}>  $params
     */
    protected function formatParameters(array $params): string
    {
        if (empty($params)) {
            return '';
        }

        $formatted = [];
        foreach ($params as $param) {
            $formatted[] = $param['type'].' $'.$param['name'];
        }

        return implode(', ', $formatted);
    }

    /**
     * Generate toModel method.
     *
     * @param  array<string, array<string, mixed>>  $properties
     */
    public function generateToModel(array $properties, string $modelClass = 'Model'): string
    {
        $body = $this->modelMapper->generateToModelBody($properties, $modelClass);

        return $this->renderer->render('method', [
            'docBlock' => $this->generateDocBlock(
                'Convert to a Laravel model instance.',
                $modelClass,
                []
            ),
            'visibility' => 'public ',
            'static' => '',
            'name' => 'toModel',
            'parameters' => '',
            'returnType' => ': '.$modelClass,
            'body' => $body,
        ]);
    }

    /**
     * Generate fromModel method.
     *
     * @param  array<string, array<string, mixed>>  $properties
     */
    public function generateFromModel(array $properties, string $modelClass = 'Model'): string
    {
        $body = $this->modelMapper->generateFromModelBody($properties);

        return $this->renderer->render('method', [
            'docBlock' => $this->generateDocBlock(
                'Create an instance from a Laravel model.',
                'static',
                [
                    ['name' => 'model', 'type' => $modelClass, 'description' => 'The model instance'],
                ]
            ),
            'visibility' => 'public ',
            'static' => 'static ',
            'name' => 'fromModel',
            'parameters' => $modelClass.' $model',
            'returnType' => ': static',
            'body' => $body,
        ]);
    }

    /**
     * Get the model mapper.
     */
    public function getModelMapper(): ModelMapper
    {
        return $this->modelMapper;
    }

    /**
     * Get the extension manager.
     */
    public function getExtensions(): ExtensionManager
    {
        return $this->extensions;
    }
}
