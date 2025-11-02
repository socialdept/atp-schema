<?php

namespace SocialDept\Schema\Generator;

class TypeMapper
{
    /**
     * Naming converter instance.
     */
    protected NamingConverter $naming;

    /**
     * Create a new TypeMapper.
     */
    public function __construct(?NamingConverter $naming = null)
    {
        $this->naming = $naming ?? new NamingConverter();
    }

    /**
     * Map lexicon type to PHP type.
     *
     * @param  array<string, mixed>  $definition
     */
    public function toPhpType(array $definition, bool $nullable = false): string
    {
        $type = $definition['type'] ?? 'unknown';

        $phpType = match ($type) {
            'string' => 'string',
            'integer' => 'int',
            'boolean' => 'bool',
            'number' => 'float',
            'array' => $this->mapArrayType($definition),
            'object' => $this->mapObjectType($definition),
            'blob' => '\\SocialDept\\Schema\\Data\\BlobReference',
            'bytes' => 'string',
            'cid-link' => 'string',
            'unknown' => 'mixed',
            'ref' => $this->mapRefType($definition),
            'union' => $this->mapUnionType($definition),
            default => 'mixed',
        };

        if ($nullable && $phpType !== 'mixed') {
            return '?'.$phpType;
        }

        return $phpType;
    }

    /**
     * Map lexicon type to PHPDoc type.
     *
     * @param  array<string, mixed>  $definition
     */
    public function toPhpDocType(array $definition, bool $nullable = false): string
    {
        $type = $definition['type'] ?? 'unknown';

        $docType = match ($type) {
            'string' => 'string',
            'integer' => 'int',
            'boolean' => 'bool',
            'number' => 'float',
            'array' => $this->mapArrayDocType($definition),
            'object' => $this->mapObjectDocType($definition),
            'blob' => '\\SocialDept\\Schema\\Data\\BlobReference',
            'bytes' => 'string',
            'cid-link' => 'string',
            'unknown' => 'mixed',
            'ref' => $this->mapRefDocType($definition),
            'union' => $this->mapUnionDocType($definition),
            default => 'mixed',
        };

        if ($nullable && $docType !== 'mixed') {
            return $docType.'|null';
        }

        return $docType;
    }

    /**
     * Map array type.
     *
     * @param  array<string, mixed>  $definition
     */
    protected function mapArrayType(array $definition): string
    {
        return 'array';
    }

    /**
     * Map array type for PHPDoc.
     *
     * @param  array<string, mixed>  $definition
     */
    protected function mapArrayDocType(array $definition): string
    {
        if (! isset($definition['items'])) {
            return 'array';
        }

        $itemType = $this->toPhpDocType($definition['items']);

        return "array<{$itemType}>";
    }

    /**
     * Map object type.
     *
     * @param  array<string, mixed>  $definition
     */
    protected function mapObjectType(array $definition): string
    {
        return 'array';
    }

    /**
     * Map object type for PHPDoc.
     *
     * @param  array<string, mixed>  $definition
     */
    protected function mapObjectDocType(array $definition): string
    {
        if (! isset($definition['properties'])) {
            return 'array';
        }

        // Build array shape annotation
        $properties = [];
        foreach ($definition['properties'] as $key => $propDef) {
            $propType = $this->toPhpDocType($propDef);
            $properties[] = "{$key}: {$propType}";
        }

        if (empty($properties)) {
            return 'array';
        }

        return 'array{'.implode(', ', $properties).'}';
    }

    /**
     * Map reference type.
     *
     * @param  array<string, mixed>  $definition
     */
    protected function mapRefType(array $definition): string
    {
        if (! isset($definition['ref'])) {
            return 'mixed';
        }

        // Convert NSID reference to class name
        return '\\'.$this->naming->nsidToClassName($definition['ref']);
    }

    /**
     * Map reference type for PHPDoc.
     *
     * @param  array<string, mixed>  $definition
     */
    protected function mapRefDocType(array $definition): string
    {
        return $this->mapRefType($definition);
    }

    /**
     * Map union type.
     *
     * @param  array<string, mixed>  $definition
     */
    protected function mapUnionType(array $definition): string
    {
        // For runtime type hints, unions of different types must be 'mixed'
        return 'mixed';
    }

    /**
     * Map union type for PHPDoc.
     *
     * @param  array<string, mixed>  $definition
     */
    protected function mapUnionDocType(array $definition): string
    {
        if (! isset($definition['refs'])) {
            return 'mixed';
        }

        $types = array_map(
            fn ($ref) => '\\'.$this->naming->nsidToClassName($ref),
            $definition['refs']
        );

        return implode('|', $types);
    }

    /**
     * Check if type is nullable based on definition.
     *
     * @param  array<string, mixed>  $definition
     */
    public function isNullable(array $definition, array $required = []): bool
    {
        // Check if explicitly marked as required
        if (isset($definition['required']) && $definition['required'] === true) {
            return false;
        }

        // Check if in required array
        if (! empty($required)) {
            return false;
        }

        // Default to nullable for optional fields
        return true;
    }

    /**
     * Get default value for a type.
     *
     * @param  array<string, mixed>  $definition
     */
    public function getDefaultValue(array $definition): ?string
    {
        if (! array_key_exists('default', $definition)) {
            return null;
        }

        $default = $definition['default'];

        if ($default === null) {
            return 'null';
        }

        if (is_string($default)) {
            return "'".addslashes($default)."'";
        }

        if (is_bool($default)) {
            return $default ? 'true' : 'false';
        }

        if (is_numeric($default)) {
            return (string) $default;
        }

        if (is_array($default)) {
            return '[]';
        }

        return null;
    }

    /**
     * Check if type needs use statement.
     *
     * @param  array<string, mixed>  $definition
     */
    public function needsUseStatement(array $definition): bool
    {
        $type = $definition['type'] ?? 'unknown';

        return in_array($type, ['ref', 'blob']);
    }

    /**
     * Get use statements for type.
     *
     * @param  array<string, mixed>  $definition
     * @return array<string>
     */
    public function getUseStatements(array $definition): array
    {
        $type = $definition['type'] ?? 'unknown';

        if ($type === 'blob') {
            return ['SocialDept\\Schema\\Data\\BlobReference'];
        }

        if ($type === 'ref' && isset($definition['ref'])) {
            return [$this->naming->nsidToClassName($definition['ref'])];
        }

        if ($type === 'union' && isset($definition['refs'])) {
            return array_map(
                fn ($ref) => $this->naming->nsidToClassName($ref),
                $definition['refs']
            );
        }

        return [];
    }
}
