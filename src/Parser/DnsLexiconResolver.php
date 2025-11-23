<?php

namespace SocialDept\Schema\Parser;

use Illuminate\Support\Facades\Http;
use SocialDept\Schema\Contracts\LexiconParser;
use SocialDept\Schema\Contracts\LexiconResolver;
use SocialDept\Schema\Data\LexiconDocument;
use SocialDept\Schema\Exceptions\SchemaNotFoundException;

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
     * Whether the atp-resolver package is available.
     */
    protected bool $hasResolver;

    /**
     * Whether we've shown the resolver warning.
     */
    protected static bool $resolverWarningShown = false;

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
        $this->parser = $parser ?? new DefaultLexiconParser;
        $this->hasResolver = class_exists('SocialDept\\Resolver\\Resolver');
    }

    /**
     * Resolve NSID to Lexicon schema via DNS and XRPC.
     */
    public function resolve(string $nsid): LexiconDocument
    {
        if (! $this->enabled) {
            throw SchemaNotFoundException::forNsid($nsid);
        }

        if (! $this->hasResolver) {
            $this->showResolverWarning();
            throw SchemaNotFoundException::forNsid($nsid);
        }

        try {
            $nsidParsed = Nsid::parse($nsid);

            // Step 1: Query DNS TXT record for DID
            $did = $this->lookupDns($nsidParsed->getAuthority());
            if ($did === null) {
                throw SchemaNotFoundException::forNsid($nsid);
            }

            // Step 2: Resolve DID to PDS endpoint
            $pdsUrl = $this->resolvePdsEndpoint($did);
            if ($pdsUrl === null) {
                throw SchemaNotFoundException::forNsid($nsid);
            }

            // Step 3: Fetch lexicon schema from repository
            $schema = $this->retrieveSchema($pdsUrl, $did, $nsid);

            return $this->parser->parseArray($schema);
        } catch (SchemaNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw SchemaNotFoundException::forNsid($nsid);
        }
    }

    /**
     * Perform DNS TXT lookup for _lexicon.{authority}.
     */
    public function lookupDns(string $authority): ?string
    {
        // Convert authority to domain (e.g., pub.leaflet -> leaflet.pub)
        $parts = explode('.', $authority);
        $domain = implode('.', array_reverse($parts));

        // Query DNS TXT record at _lexicon.<domain>
        $hostname = "_lexicon.{$domain}";

        try {
            $records = dns_get_record($hostname, DNS_TXT);

            if ($records === false || empty($records)) {
                return null;
            }

            // Look for TXT record with did= prefix
            foreach ($records as $record) {
                if (isset($record['txt']) && str_starts_with($record['txt'], 'did=')) {
                    return substr($record['txt'], 4); // Remove 'did=' prefix
                }
            }
        } catch (\Exception $e) {
            // DNS query failed
            return null;
        }

        return null;
    }

    /**
     * Retrieve schema via XRPC from PDS.
     */
    public function retrieveSchema(string $pdsEndpoint, string $did, string $nsid): array
    {
        try {
            // Construct XRPC call to com.atproto.repo.getRecord
            $response = Http::timeout($this->httpTimeout)
                ->get("{$pdsEndpoint}/xrpc/com.atproto.repo.getRecord", [
                    'repo' => $did,
                    'collection' => 'com.atproto.lexicon.schema',
                    'rkey' => $nsid,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                // Extract the lexicon schema from the record value
                if (isset($data['value']) && is_array($data['value']) && isset($data['value']['lexicon'])) {
                    return $data['value'];
                }
            }
        } catch (\Exception $e) {
            throw SchemaNotFoundException::forNsid($nsid);
        }

        throw SchemaNotFoundException::forNsid($nsid);
    }

    /**
     * Check if DNS resolution is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Resolve DID to PDS endpoint using atp-resolver.
     */
    protected function resolvePdsEndpoint(string $did): ?string
    {
        if (! $this->hasResolver) {
            return null;
        }

        try {
            // Get resolver from Laravel container if available
            if (function_exists('app') && app()->has(\SocialDept\Resolver\Resolver::class)) {
                $resolver = app(\SocialDept\Resolver\Resolver::class);
            } else {
                // Can't instantiate without dependencies
                return null;
            }

            // Use the resolvePds method which handles DID resolution and PDS extraction
            return $resolver->resolvePds($did);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Show warning about missing atp-resolver package.
     */
    protected function showResolverWarning(): void
    {
        if (self::$resolverWarningShown) {
            return;
        }

        if (function_exists('logger')) {
            logger()->warning(
                'DNS-based lexicon resolution requires the socialdept/atp-resolver package. '.
                'Install it with: composer require socialdept/atp-resolver '.
                'Falling back to local lexicon sources only.'
            );
        }

        self::$resolverWarningShown = true;
    }
}
