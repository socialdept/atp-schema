<?php

namespace SocialDept\AtpSchema\Generator;

use SocialDept\AtpSchema\Support\ExtensionManager;

class TypeMapper
{
    /**
     * Naming converter instance.
     */
    protected NamingConverter $naming;

    /**
     * Local definition map for resolving #refs.
     *
     * @var array<string, string>
     */
    protected array $localDefinitions = [];

    /**
     * Extension manager instance.
     */
    protected ExtensionManager $extensions;

    /**
     * Create a new TypeMapper.
     */
    public function __construct(?NamingConverter $naming = null, ?ExtensionManager $extensions = null)
    {
        $this->naming = $naming ?? new NamingConverter();
        $this->extensions = $extensions ?? new ExtensionManager();
    }

    /**
     * Set local definitions for resolving local references.
     *
     * @param  array<string, string>  $localDefinitions  Map of #ref => class name
     */
    public function setLocalDefinitions(array $localDefinitions): void
    {
        $this->localDefinitions = $localDefinitions;
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
            'string' => $this->mapStringType($definition),
            'integer' => 'int',
            'boolean' => 'bool',
            'number' => 'float',
            'array' => $this->mapArrayType($definition),
            'object' => $this->mapObjectType($definition),
            'blob' => 'BlobReference',
            'bytes' => 'string',
            'cid-link' => 'string',
            'unknown' => 'mixed',
            'ref' => $this->mapRefType($definition),
            'union' => $this->mapUnionType($definition),
            default => 'mixed',
        };

        if ($nullable && $phpType !== 'mixed') {
            $phpType = '?'.$phpType;
        }

        return $this->extensions->filter('filter:type:phpType', $phpType, $definition, $nullable);
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
            'string' => $this->mapStringType($definition),
            'integer' => 'int',
            'boolean' => 'bool',
            'number' => 'float',
            'array' => $this->mapArrayDocType($definition),
            'object' => $this->mapObjectDocType($definition),
            'blob' => 'BlobReference',
            'bytes' => 'string',
            'cid-link' => 'string',
            'unknown' => 'mixed',
            'ref' => $this->mapRefDocType($definition),
            'union' => $this->mapUnionDocType($definition),
            default => 'mixed',
        };

        if ($nullable && $docType !== 'mixed') {
            $docType = $docType.'|null';
        }

        return $this->extensions->filter('filter:type:phpDocType', $docType, $definition, $nullable);
    }

    /**
     * Map string type.
     *
     * @param  array<string, mixed>  $definition
     */
    protected function mapStringType(array $definition): string
    {
        // Check for datetime format
        if (isset($definition['format']) && $definition['format'] === 'datetime') {
            return 'Carbon';
        }

        return 'string';
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

        // array<mixed> is redundant, just use array
        if ($itemType === 'mixed') {
            return 'array';
        }

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

        $ref = $definition['ref'];

        // Resolve local references using the local definitions map
        if (str_starts_with($ref, '#')) {
            return $this->localDefinitions[$ref] ?? 'mixed';
        }

        // Handle NSID fragments (e.g., com.atproto.label.defs#selfLabels)
        // Convert fragment to class name
        if (str_contains($ref, '#')) {
            [$baseNsid, $fragment] = explode('#', $ref, 2);

            return $this->naming->toClassName($fragment);
        }

        // Convert NSID reference to fully qualified class name
        $fqcn = $this->naming->nsidToClassName($ref);

        // Extract short class name (last part after final backslash)
        $parts = explode('\\', $fqcn);

        return end($parts);
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
        // Open unions (closed=false or not set) should always be mixed
        // because future schema versions could add more types
        $isClosed = $definition['closed'] ?? false;

        if (! $isClosed) {
            return 'mixed';
        }

        // For closed unions, extract external refs
        $refs = $definition['refs'] ?? [];
        $externalRefs = array_values(array_filter($refs, fn ($ref) => ! str_starts_with($ref, '#')));

        if (empty($externalRefs)) {
            return 'mixed';
        }

        // Build union type with all variants
        $types = [];
        foreach ($externalRefs as $ref) {
            // Handle NSID fragments - convert fragment to class name
            if (str_contains($ref, '#')) {
                [$baseNsid, $fragment] = explode('#', $ref, 2);
                $types[] = $this->naming->toClassName($fragment);
            } else {
                // Convert to fully qualified class name, then extract short name
                $fqcn = $this->naming->nsidToClassName($ref);
                $parts = explode('\\', $fqcn);
                $types[] = end($parts);
            }
        }

        // Return union type (e.g., "Theme|ThemeV2" or just "Theme" for single ref)
        return implode('|', $types);
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

        // Open unions should be typed as mixed since future types could be added
        $isClosed = $definition['closed'] ?? false;
        if (! $isClosed) {
            return 'mixed';
        }

        // For closed unions, list all the specific types
        $types = [];
        foreach ($definition['refs'] as $ref) {
            // Resolve local references using the local definitions map
            if (str_starts_with($ref, '#')) {
                $types[] = $this->localDefinitions[$ref] ?? 'mixed';

                continue;
            }

            // Handle NSID fragments - convert fragment to class name
            if (str_contains($ref, '#')) {
                [$baseNsid, $fragment] = explode('#', $ref, 2);
                $types[] = $this->naming->toClassName($fragment);

                continue;
            }

            // Convert to fully qualified class name, then extract short name
            $fqcn = $this->naming->nsidToClassName($ref);
            $parts = explode('\\', $fqcn);
            $types[] = end($parts);
        }

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

        // Check for datetime format on strings
        if ($type === 'string' && isset($definition['format']) && $definition['format'] === 'datetime') {
            return true;
        }

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

        if ($type === 'string' && isset($definition['format']) && $definition['format'] === 'datetime') {
            return ['Carbon\\Carbon'];
        }

        if ($type === 'blob') {
            return ['SocialDept\\AtpSchema\\Data\\BlobReference'];
        }

        if ($type === 'ref' && isset($definition['ref'])) {
            $ref = $definition['ref'];

            // Skip local references (starting with #)
            if (str_starts_with($ref, '#')) {
                return [];
            }

            // Handle NSID fragments - convert fragment to class name
            if (str_contains($ref, '#')) {
                [$baseNsid, $fragment] = explode('#', $ref, 2);
                // For fragments, we need to include ALL segments of the base NSID
                // Parse the NSID and convert each segment to PascalCase
                $nsid = \SocialDept\AtpSupport\Nsid::parse($baseNsid);
                $segments = $nsid->getSegments();
                $namespaceParts = array_map(
                    fn ($part) => $this->naming->toPascalCase($part),
                    $segments
                );
                $namespace = $this->naming->getBaseNamespace() . '\\' . implode('\\', $namespaceParts);
                $className = $this->naming->toClassName($fragment);

                return [$namespace . '\\' . $className];
            }

            return [$this->naming->nsidToClassName($ref)];
        }

        if ($type === 'union' && isset($definition['refs'])) {
            // Open unions don't need use statements since they're typed as mixed
            $isClosed = $definition['closed'] ?? false;
            if (! $isClosed) {
                return [];
            }

            // For closed unions, import the referenced classes
            $classes = [];

            foreach ($definition['refs'] as $ref) {
                // Skip local references
                if (str_starts_with($ref, '#')) {
                    continue;
                }

                // Handle NSID fragments - convert fragment to class name
                if (str_contains($ref, '#')) {
                    [$baseNsid, $fragment] = explode('#', $ref, 2);
                    // For fragments, we need to include ALL segments of the base NSID
                    $nsid = \SocialDept\AtpSupport\Nsid::parse($baseNsid);
                    $segments = $nsid->getSegments();
                    $namespaceParts = array_map(
                        fn ($part) => $this->naming->toPascalCase($part),
                        $segments
                    );
                    $namespace = $this->naming->getBaseNamespace() . '\\' . implode('\\', $namespaceParts);
                    $className = $this->naming->toClassName($fragment);
                    $classes[] = $namespace . '\\' . $className;
                } else {
                    $classes[] = $this->naming->nsidToClassName($ref);
                }
            }

            return $classes;
        }

        $uses = [];

        return $this->extensions->filter('filter:type:useStatements', $uses, $definition);
    }

    /**
     * Get the extension manager.
     */
    public function getExtensions(): ExtensionManager
    {
        return $this->extensions;
    }
}
