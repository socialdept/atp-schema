<?php

namespace SocialDept\Schema\Contracts;

use SocialDept\Schema\Data\LexiconDocument;

interface LexiconValidator
{
    /**
     * Validate data against Lexicon schema.
     */
    public function validate(array $data, LexiconDocument $schema): bool;

    /**
     * Validate and return errors.
     *
     * @return array<string, array<string>>
     */
    public function validateWithErrors(array $data, LexiconDocument $schema): array;

    /**
     * Validate a specific field.
     */
    public function validateField(mixed $value, string $field, LexiconDocument $schema): bool;

    /**
     * Set validation mode (strict, optimistic, lenient).
     */
    public function setMode(string $mode): void;
}
