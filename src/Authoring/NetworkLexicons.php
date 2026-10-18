<?php

namespace SocialDept\AtpSchema\Authoring;

use Closure;
use RuntimeException;
use SocialDept\AtpSupport\Data\PublishedLexicon as SupportPublishedLexicon;
use SocialDept\AtpSupport\Exceptions\LexiconResolutionException;
use SocialDept\AtpSupport\Nsid;
use SocialDept\AtpSupport\Resolver;
use SocialDept\AtpSupport\Resolvers\LexiconDnsResolver;

/**
 * Lexicons as the network publishes them, for the authoring commands.
 *
 * Resolution itself is atp-support's LexiconDnsResolver: the TXT record at
 * _lexicon.<authority domain> names a DID, and the schema is that repo's
 * com.atproto.lexicon.schema record keyed by the NSID. Resolution is not
 * hierarchical, so every authority level needs its own TXT record.
 */
class NetworkLexicons
{
    public const COLLECTION = LexiconDnsResolver::COLLECTION;

    /**
     * @param  (Closure(string): ?string)|null  $dns  Looks up the DID for a TXT name, for tests
     */
    public function __construct(
        protected ?Closure $dns = null,
        protected int $timeout = 10,
    ) {
    }

    /**
     * The DNS name whose TXT record names the NSID's authority DID.
     */
    public static function dnsName(string $nsid): string
    {
        return Nsid::parse($nsid)->lexiconDnsName();
    }

    /**
     * The DID the NSID's authority names in DNS, if any.
     */
    public function authority(string $nsid): ?string
    {
        return $this->resolver()->lookupDns(Nsid::parse($nsid)->getAuthority());
    }

    /**
     * The published schema, or null when the authority names no DID or has
     * not published one.
     *
     * @throws RuntimeException when the authority's PDS cannot be read
     */
    public function fetch(string $nsid): ?PublishedLexicon
    {
        return $this->convert(fn (LexiconDnsResolver $resolver) => $resolver->fetch($nsid));
    }

    /**
     * The schema a given repo publishes for an NSID, or null if it has none.
     *
     * @throws RuntimeException when the repo's PDS cannot be read
     */
    public function fetchFrom(string $did, string $nsid): ?PublishedLexicon
    {
        return $this->convert(fn (LexiconDnsResolver $resolver) => $resolver->fetchFrom($did, $nsid));
    }

    /**
     * @param  Closure(LexiconDnsResolver): ?SupportPublishedLexicon  $lookup
     */
    protected function convert(Closure $lookup): ?PublishedLexicon
    {
        try {
            $published = $lookup($this->resolver());
        } catch (LexiconResolutionException $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }

        return $published === null ? null : new PublishedLexicon(
            nsid: $published->nsid,
            did: $published->did,
            uri: $published->uri,
            cid: $published->cid,
            lexicon: $published->schema,
        );
    }

    protected function resolver(): LexiconDnsResolver
    {
        $dns = $this->dns;

        return new LexiconDnsResolver(
            resolver: app(Resolver::class),
            httpTimeout: $this->timeout,
            dnsLookup: $dns === null ? null : function (string $hostname) use ($dns) {
                $did = $dns($hostname);

                return $did === null ? false : [['txt' => "did={$did}"]];
            },
        );
    }
}
