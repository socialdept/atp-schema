<?php

namespace SocialDept\AtpSchema\Generator;

use SocialDept\AtpSchema\Parser\Nsid;

class NamespaceResolver
{
    /**
     * Base namespace for generated classes.
     */
    protected string $baseNamespace;

    /**
     * Create a new NamespaceResolver.
     */
    public function __construct(string $baseNamespace = 'App\\Lexicons')
    {
        $this->baseNamespace = rtrim($baseNamespace, '\\');
    }

    /**
     * Resolve NSID to PHP namespace.
     */
    public function resolveNamespace(string $nsidString): string
    {
        $nsid = Nsid::parse($nsidString);

        // Convert authority to namespace parts (com.example.feed -> Com\Example\Feed)
        $parts = explode('.', $nsid->getAuthority());
        $namespaceParts = array_map(fn ($part) => $this->toPascalCase($part), $parts);

        return $this->baseNamespace.'\\'.implode('\\', $namespaceParts);
    }

    /**
     * Resolve NSID to PHP class name.
     */
    public function resolveClassName(string $nsidString, ?string $defName = null): string
    {
        $nsid = Nsid::parse($nsidString);

        // Use definition name if provided, otherwise use the name from NSID
        $name = $defName ?? $nsid->getName();

        return $this->toPascalCase($name);
    }

    /**
     * Resolve full qualified class name.
     */
    public function resolveFullyQualifiedName(string $nsidString, ?string $defName = null): string
    {
        $namespace = $this->resolveNamespace($nsidString);
        $className = $this->resolveClassName($nsidString, $defName);

        return $namespace.'\\'.$className;
    }

    /**
     * Convert string to PascalCase.
     */
    protected function toPascalCase(string $string): string
    {
        // Replace dots, hyphens, and underscores with spaces
        $string = str_replace(['.', '-', '_'], ' ', $string);

        // Capitalize each word
        $string = ucwords($string);

        // Remove spaces
        return str_replace(' ', '', $string);
    }

    /**
     * Convert NSID to file path.
     */
    public function resolveFilePath(string $nsidString, string $baseDirectory, ?string $defName = null): string
    {
        $namespace = $this->resolveNamespace($nsidString);
        $className = $this->resolveClassName($nsidString, $defName);

        // Remove base namespace from full namespace
        $relativePath = str_replace($this->baseNamespace.'\\', '', $namespace);
        $relativePath = str_replace('\\', '/', $relativePath);

        return rtrim($baseDirectory, '/').'/'.$relativePath.'/'.$className.'.php';
    }

    /**
     * Get the base namespace.
     */
    public function getBaseNamespace(): string
    {
        return $this->baseNamespace;
    }
}
