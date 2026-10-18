<?php

namespace SocialDept\AtpSchema\Authoring;

use Closure;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use SocialDept\AtpSupport\Facades\Resolver;
use SocialDept\AtpSupport\Nsid;
use SocialDept\AtpSupport\Resolvers\LexiconDnsResolver;

/**
 * Lexicons as the network publishes them.
 *
 * An NSID's authority publishes a TXT record at _lexicon.<authority domain>
 * holding did=<DID>, and the schema as a com.atproto.lexicon.schema record,
 * keyed by the NSID, in that DID's repo. Resolution is not hierarchical:
 * every authority level needs its own TXT record.
 */
class NetworkLexicons
{
    public const COLLECTION = 'com.atproto.lexicon.schema';

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
        return '_lexicon.'.Nsid::parse($nsid)->getAuthorityDomain();
    }

    /**
     * The DID the NSID's authority names in DNS, if any.
     */
    public function authority(string $nsid): ?string
    {
        if ($this->dns !== null) {
            return ($this->dns)(self::dnsName($nsid));
        }

        $parsed = Nsid::parse($nsid);

        return (new LexiconDnsResolver(app(\SocialDept\AtpSupport\Resolver::class), $this->timeout))->lookupDns($parsed->getAuthority());
    }

    /**
     * The published schema, or null when the authority names no DID or has
     * not published one.
     *
     * @throws RuntimeException when the authority's PDS cannot be read
     */
    public function fetch(string $nsid): ?PublishedLexicon
    {
        $did = $this->authority($nsid);

        return $did === null ? null : $this->fetchFrom($did, $nsid);
    }

    /**
     * The schema a given repo publishes for an NSID, or null if it has none.
     *
     * @throws RuntimeException when the repo's PDS cannot be read
     */
    public function fetchFrom(string $did, string $nsid): ?PublishedLexicon
    {
        $pds = Resolver::resolvePds($did) ?? throw new RuntimeException("{$did} names no PDS.");

        $response = Http::timeout($this->timeout)
            ->acceptJson()
            ->get(rtrim($pds, '/').'/xrpc/com.atproto.repo.getRecord', [
                'repo' => $did,
                'collection' => self::COLLECTION,
                'rkey' => $nsid,
            ]);

        if ($response->status() === 400 && in_array($response->json('error'), ['RecordNotFound', 'NotFound'], true)) {
            return null;
        }

        if (! $response->successful() || ! is_array($value = $response->json('value'))) {
            throw new RuntimeException("Could not read {$nsid} from {$pds}: HTTP {$response->status()}.");
        }

        unset($value['$type']);

        return new PublishedLexicon(
            nsid: $nsid,
            did: $did,
            uri: (string) ($response->json('uri') ?? 'at://'.$did.'/'.self::COLLECTION.'/'.$nsid),
            cid: $response->json('cid'),
            lexicon: $value,
        );
    }
}
