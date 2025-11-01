<?php

namespace SocialDept\Schema\Contracts;

use SocialDept\Schema\Data\LexiconDocument;

interface LexiconResolver
{
    /**
     * Resolve NSID to Lexicon schema via DNS and XRPC.
     */
    public function resolve(string $nsid): LexiconDocument;

    /**
     * Perform DNS TXT lookup for _lexicon.{authority}.
     */
    public function lookupDns(string $authority): ?string;

    /**
     * Retrieve schema via XRPC from PDS.
     */
    public function retrieveSchema(string $pdsEndpoint, string $did, string $nsid): array;

    /**
     * Check if DNS resolution is enabled.
     */
    public function isEnabled(): bool;
}
