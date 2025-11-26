<?php

namespace SocialDept\AtpSchema\Contracts;

use SocialDept\AtpSchema\Data\LexiconDocument;

interface LexiconParser
{
    /**
     * Parse raw Lexicon JSON into structured objects.
     */
    public function parse(string $json): LexiconDocument;

    /**
     * Parse Lexicon from array data.
     */
    public function parseArray(array $data): LexiconDocument;

    /**
     * Validate Lexicon schema structure.
     */
    public function validate(array $data): bool;

    /**
     * Resolve $ref references to other schemas.
     */
    public function resolveReference(string $ref, LexiconDocument $context): mixed;
}
