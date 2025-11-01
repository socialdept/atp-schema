<?php

namespace SocialDept\Schema\Contracts;

interface TypeMapper
{
    /**
     * Map Lexicon type to PHP type.
     */
    public function toPhpType(string $lexiconType): string;

    /**
     * Map Lexicon type to PHPDoc type.
     */
    public function toPhpDocType(string $lexiconType): string;

    /**
     * Handle union types.
     *
     * @param  array<string>  $types
     */
    public function unionType(array $types): string;

    /**
     * Check if type is nullable.
     */
    public function isNullable(array $definition): bool;

    /**
     * Resolve type reference.
     */
    public function resolveReference(string $ref): string;
}
