<?php

namespace SocialDept\Schema\Generator;

class ModelMapper
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
     * Create a new ModelMapper.
     */
    public function __construct(?NamingConverter $naming = null, ?TypeMapper $typeMapper = null)
    {
        $this->naming = $naming ?? new NamingConverter();
        $this->typeMapper = $typeMapper ?? new TypeMapper($this->naming);
    }

    /**
     * Generate toModel method body.
     *
     * @param  array<string, array<string, mixed>>  $properties
     */
    public function generateToModelBody(array $properties, string $modelClass = 'Model'): string
    {
        if (empty($properties)) {
            return "        return new {$modelClass}();";
        }

        $lines = [];
        $lines[] = "        return new {$modelClass}([";

        foreach ($properties as $name => $definition) {
            $mapping = $this->generatePropertyToModel($name, $definition);
            $lines[] = "            '{$name}' => {$mapping},";
        }

        $lines[] = '        ]);';

        return implode("\n", $lines);
    }

    /**
     * Generate fromModel method body.
     *
     * @param  array<string, array<string, mixed>>  $properties
     */
    public function generateFromModelBody(array $properties): string
    {
        if (empty($properties)) {
            return '        return new static();';
        }

        $lines = [];
        $lines[] = '        return new static(';

        foreach ($properties as $name => $definition) {
            $mapping = $this->generatePropertyFromModel($name, $definition);
            $lines[] = "            {$name}: {$mapping},";
        }

        // Remove trailing comma from last line
        $lastIndex = count($lines) - 1;
        $lines[$lastIndex] = rtrim($lines[$lastIndex], ',');

        $lines[] = '        );';

        return implode("\n", $lines);
    }

    /**
     * Generate property mapping to model.
     *
     * @param  array<string, mixed>  $definition
     */
    protected function generatePropertyToModel(string $name, array $definition): string
    {
        $type = $definition['type'] ?? 'unknown';

        // Handle DateTime types
        if ($type === 'string' && isset($definition['format']) && $definition['format'] === 'datetime') {
            return "\$this->{$name}?->format('Y-m-d H:i:s')";
        }

        // Handle blob types
        if ($type === 'blob') {
            return "\$this->{$name}?->toArray()";
        }

        // Handle nested refs
        if ($type === 'ref') {
            return "\$this->{$name}?->toArray()";
        }

        // Handle arrays of refs
        if ($type === 'array' && isset($definition['items']['type']) && $definition['items']['type'] === 'ref') {
            return "array_map(fn (\$item) => \$item->toArray(), \$this->{$name} ?? [])";
        }

        // Handle arrays of objects
        if ($type === 'array' && isset($definition['items']['type']) && $definition['items']['type'] === 'object') {
            return "\$this->{$name} ?? []";
        }

        // Simple property
        return "\$this->{$name}";
    }

    /**
     * Generate property mapping from model.
     *
     * @param  array<string, mixed>  $definition
     */
    protected function generatePropertyFromModel(string $name, array $definition): string
    {
        $type = $definition['type'] ?? 'unknown';

        // Handle DateTime types
        if ($type === 'string' && isset($definition['format']) && $definition['format'] === 'datetime') {
            return "\$model->{$name} ? new \\DateTime(\$model->{$name}) : null";
        }

        // Handle blob types
        if ($type === 'blob') {
            return "\$model->{$name} ? \\SocialDept\\Schema\\Data\\BlobReference::fromArray(\$model->{$name}) : null";
        }

        // Handle nested refs
        if ($type === 'ref' && isset($definition['ref'])) {
            $refClass = $this->naming->nsidToClassName($definition['ref']);
            $className = basename(str_replace('\\', '/', $refClass));

            return "\$model->{$name} ? {$className}::fromArray(\$model->{$name}) : null";
        }

        // Handle arrays of refs
        if ($type === 'array' && isset($definition['items']['type']) && $definition['items']['type'] === 'ref') {
            $refClass = $this->naming->nsidToClassName($definition['items']['ref']);
            $className = basename(str_replace('\\', '/', $refClass));

            return "\$model->{$name} ? array_map(fn (\$item) => {$className}::fromArray(\$item), \$model->{$name}) : []";
        }

        // Simple property with null coalescing
        return "\$model->{$name} ?? null";
    }

    /**
     * Get field mapping configuration.
     *
     * @param  array<string, array<string, mixed>>  $properties
     * @return array<string, string>
     */
    public function getFieldMapping(array $properties): array
    {
        $mapping = [];

        foreach ($properties as $name => $definition) {
            // Convert camelCase to snake_case for database columns
            $mapping[$name] = $this->naming->toSnakeCase($name);
        }

        return $mapping;
    }

    /**
     * Check if property needs special handling.
     *
     * @param  array<string, mixed>  $definition
     */
    public function needsTransformer(array $definition): bool
    {
        $type = $definition['type'] ?? 'unknown';

        if ($type === 'blob') {
            return true;
        }

        if ($type === 'ref') {
            return true;
        }

        if ($type === 'string' && isset($definition['format']) && $definition['format'] === 'datetime') {
            return true;
        }

        if ($type === 'array' && isset($definition['items']['type'])) {
            $itemType = $definition['items']['type'];
            if (in_array($itemType, ['ref', 'object'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get transformer type for property.
     *
     * @param  array<string, mixed>  $definition
     */
    public function getTransformerType(array $definition): ?string
    {
        $type = $definition['type'] ?? 'unknown';

        if ($type === 'string' && isset($definition['format']) && $definition['format'] === 'datetime') {
            return 'datetime';
        }

        if ($type === 'blob') {
            return 'blob';
        }

        if ($type === 'ref') {
            return 'ref';
        }

        if ($type === 'array' && isset($definition['items']['type'])) {
            $itemType = $definition['items']['type'];
            if ($itemType === 'ref') {
                return 'array_ref';
            }
            if ($itemType === 'object') {
                return 'array_object';
            }
        }

        return null;
    }
}
