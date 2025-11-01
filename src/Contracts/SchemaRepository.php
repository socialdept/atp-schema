<?php

namespace SocialDept\Schema\Contracts;

use SocialDept\Schema\Data\LexiconDocument;

interface SchemaRepository
{
    /**
     * Find schema by NSID.
     */
    public function find(string $nsid): ?LexiconDocument;

    /**
     * Load schema from multiple sources.
     */
    public function load(string $nsid): LexiconDocument;

    /**
     * Check if schema exists.
     */
    public function exists(string $nsid): bool;

    /**
     * Get all available schema NSIDs.
     *
     * @return array<string>
     */
    public function all(): array;

    /**
     * Clear cached schemas.
     */
    public function clearCache(?string $nsid = null): void;
}
