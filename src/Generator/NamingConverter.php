<?php

namespace SocialDept\AtpSchema\Generator;

use SocialDept\AtpSchema\Parser\Nsid;

class NamingConverter
{
    /**
     * Base namespace for generated classes.
     */
    protected string $baseNamespace;

    /**
     * PHP reserved keywords that cannot be used as class names.
     */
    protected const RESERVED_KEYWORDS = [
        'abstract', 'and', 'array', 'as', 'break', 'callable', 'case', 'catch',
        'class', 'clone', 'const', 'continue', 'declare', 'default', 'die', 'do',
        'echo', 'else', 'elseif', 'empty', 'enddeclare', 'endfor', 'endforeach',
        'endif', 'endswitch', 'endwhile', 'eval', 'exit', 'extends', 'final',
        'finally', 'fn', 'for', 'foreach', 'function', 'global', 'goto', 'if',
        'implements', 'include', 'include_once', 'instanceof', 'insteadof',
        'interface', 'isset', 'list', 'match', 'namespace', 'new', 'or', 'print',
        'private', 'protected', 'public', 'readonly', 'require', 'require_once',
        'return', 'static', 'switch', 'throw', 'trait', 'try', 'unset', 'use',
        'var', 'while', 'xor', 'yield', '__halt_compiler',
    ];

    /**
     * Create a new NamingConverter.
     */
    public function __construct(string $baseNamespace = 'App\\Lexicons')
    {
        $this->baseNamespace = rtrim($baseNamespace, '\\');
    }

    /**
     * Convert NSID to fully qualified class name.
     */
    public function nsidToClassName(string $nsidString): string
    {
        $nsid = Nsid::parse($nsidString);
        $namespace = $this->nsidToNamespace($nsidString);
        $className = $this->toClassName($nsid->getName());

        return $namespace.'\\'.$className;
    }

    /**
     * Convert NSID to namespace.
     */
    public function nsidToNamespace(string $nsidString): string
    {
        $nsid = Nsid::parse($nsidString);

        // Split authority into parts (e.g., "blog.pckt" -> ["blog", "pckt"])
        $authorityParts = explode('.', $nsid->getAuthority());

        // Convert each part to PascalCase
        $namespaceParts = array_map(
            fn ($part) => $this->toPascalCase($part),
            $authorityParts
        );

        return $this->baseNamespace.'\\'.implode('\\', $namespaceParts);
    }

    /**
     * Get class name from NSID name part.
     */
    public function toClassName(string $name): string
    {
        // Split on dots (e.g., "feed.post" -> "FeedPost")
        $parts = explode('.', $name);

        $className = implode('', array_map(
            fn ($part) => $this->toPascalCase($part),
            $parts
        ));

        // Check if the class name is a reserved keyword and add suffix
        if ($this->isReservedKeyword($className)) {
            $className .= 'Record';
        }

        return $className;
    }

    /**
     * Check if a name is a PHP reserved keyword.
     */
    protected function isReservedKeyword(string $name): bool
    {
        return in_array(strtolower($name), self::RESERVED_KEYWORDS, true);
    }

    /**
     * Convert to PascalCase.
     */
    public function toPascalCase(string $string): string
    {
        // Split on hyphens, underscores, or existing camelCase boundaries
        $words = preg_split('/[-_\s]+|(?=[A-Z])/', $string);

        if ($words === false) {
            $words = [$string];
        }

        // Capitalize first letter of each word
        $words = array_map(fn ($word) => ucfirst(strtolower($word)), $words);

        return implode('', $words);
    }

    /**
     * Convert to camelCase.
     */
    public function toCamelCase(string $string): string
    {
        $pascalCase = $this->toPascalCase($string);

        return lcfirst($pascalCase);
    }

    /**
     * Convert to snake_case.
     */
    public function toSnakeCase(string $string): string
    {
        // Insert underscore before capital letters, except at the start
        $snake = preg_replace('/(?<!^)[A-Z]/', '_$0', $string);

        if ($snake === null) {
            return strtolower($string);
        }

        return strtolower($snake);
    }

    /**
     * Convert to kebab-case.
     */
    public function toKebabCase(string $string): string
    {
        return str_replace('_', '-', $this->toSnakeCase($string));
    }

    /**
     * Pluralize a word (simple English rules).
     */
    public function pluralize(string $word): string
    {
        if (str_ends_with($word, 'y')) {
            return substr($word, 0, -1).'ies';
        }

        if (str_ends_with($word, 's') || str_ends_with($word, 'x') || str_ends_with($word, 'ch') || str_ends_with($word, 'sh')) {
            return $word.'es';
        }

        return $word.'s';
    }

    /**
     * Singularize a word (simple English rules).
     */
    public function singularize(string $word): string
    {
        if (str_ends_with($word, 'ies')) {
            return substr($word, 0, -3).'y';
        }

        if (str_ends_with($word, 'ses') || str_ends_with($word, 'xes') || str_ends_with($word, 'ches') || str_ends_with($word, 'shes')) {
            return substr($word, 0, -2);
        }

        if (str_ends_with($word, 's') && ! str_ends_with($word, 'ss')) {
            return substr($word, 0, -1);
        }

        return $word;
    }

    /**
     * Get the base namespace.
     */
    public function getBaseNamespace(): string
    {
        return $this->baseNamespace;
    }

    /**
     * Set the base namespace.
     */
    public function setBaseNamespace(string $namespace): void
    {
        $this->baseNamespace = rtrim($namespace, '\\');
    }
}
