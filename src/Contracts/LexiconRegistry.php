<?php

namespace SocialDept\Schema\Contracts;

use SocialDept\Schema\Data\LexiconDocument;

interface LexiconRegistry
{
    /**
     * Register a lexicon document.
     */
    public function register(LexiconDocument $document): void;

    /**
     * Get a lexicon document by NSID.
     */
    public function get(string $nsid): ?LexiconDocument;

    /**
     * Check if a lexicon document exists.
     */
    public function has(string $nsid): bool;

    /**
     * Get all registered lexicon documents.
     *
     * @return array<string, LexiconDocument>
     */
    public function all(): array;

    /**
     * Clear all registered lexicon documents.
     */
    public function clear(): void;
}
