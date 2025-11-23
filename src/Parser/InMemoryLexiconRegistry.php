<?php

namespace SocialDept\Schema\Parser;

use SocialDept\Schema\Contracts\LexiconRegistry;
use SocialDept\Schema\Data\LexiconDocument;

class InMemoryLexiconRegistry implements LexiconRegistry
{
    /**
     * Registered lexicon documents.
     *
     * @var array<string, LexiconDocument>
     */
    protected array $documents = [];

    /**
     * Register a lexicon document.
     */
    public function register(LexiconDocument $document): void
    {
        $this->documents[$document->getNsid()] = $document;
    }

    /**
     * Get a lexicon document by NSID.
     */
    public function get(string $nsid): ?LexiconDocument
    {
        return $this->documents[$nsid] ?? null;
    }

    /**
     * Check if a lexicon document exists.
     */
    public function has(string $nsid): bool
    {
        return isset($this->documents[$nsid]);
    }

    /**
     * Get all registered lexicon documents.
     *
     * @return array<string, LexiconDocument>
     */
    public function all(): array
    {
        return $this->documents;
    }

    /**
     * Clear all registered lexicon documents.
     */
    public function clear(): void
    {
        $this->documents = [];
    }
}
