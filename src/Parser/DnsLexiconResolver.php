<?php

namespace SocialDept\AtpSchema\Parser;

use SocialDept\AtpSchema\Contracts\LexiconParser;
use SocialDept\AtpSchema\Contracts\LexiconResolver;
use SocialDept\AtpSchema\Data\LexiconDocument;
use SocialDept\AtpSchema\Exceptions\SchemaNotFoundException;
use SocialDept\AtpSupport\Resolver;
use SocialDept\AtpSupport\Resolvers\LexiconDnsResolver;

class DnsLexiconResolver implements LexiconResolver
{
    /**
     * Whether DNS resolution is enabled.
     */
    protected bool $enabled;

    /**
     * HTTP timeout in seconds.
     */
    protected int $httpTimeout;

    /**
     * Lexicon parser instance.
     */
    protected LexiconParser $parser;

    /**
     * Create a new DnsLexiconResolver.
     */
    public function __construct(
        bool $enabled = true,
        int $httpTimeout = 10,
        ?LexiconParser $parser = null
    ) {
        $this->enabled = $enabled;
        $this->httpTimeout = $httpTimeout;
        $this->parser = $parser ?? new DefaultLexiconParser();
    }

    /**
     * Resolve NSID to Lexicon schema via DNS and XRPC.
     */
    public function resolve(string $nsid): LexiconDocument
    {
        if (! $this->enabled) {
            throw SchemaNotFoundException::forNsid($nsid);
        }

        try {
            $resolver = app(Resolver::class);
            $dnsResolver = new LexiconDnsResolver($resolver, $this->httpTimeout);
            $schema = $dnsResolver->resolve($nsid);

            return $this->parser->parseArray($schema);
        } catch (SchemaNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw SchemaNotFoundException::forNsid($nsid);
        }
    }

    /**
     * Check if DNS resolution is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
