<?php

namespace SocialDept\Schema\Parser;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use SocialDept\Schema\Contracts\SchemaRepository;
use SocialDept\Schema\Data\LexiconDocument;
use SocialDept\Schema\Exceptions\SchemaNotFoundException;
use SocialDept\Schema\Exceptions\SchemaParseException;

class SchemaLoader implements SchemaRepository
{
    /**
     * In-memory cache of loaded schemas for current request.
     *
     * @var array<string, array>
     */
    protected array $memoryCache = [];

    /**
     * Schema source directories.
     *
     * @var array<string>
     */
    protected array $sources;

    /**
     * Whether to use Laravel cache.
     */
    protected bool $useCache;

    /**
     * Cache TTL in seconds.
     */
    protected int $cacheTtl;

    /**
     * Cache key prefix.
     */
    protected string $cachePrefix;

    /**
     * Whether DNS resolution is enabled.
     */
    protected bool $dnsResolutionEnabled;

    /**
     * HTTP timeout for schema fetching.
     */
    protected int $httpTimeout;

    /**
     * Whether the atp-resolver package is available.
     */
    protected bool $hasResolver = false;

    /**
     * Whether we've shown the resolver warning.
     */
    protected static bool $resolverWarningShown = false;

    /**
     * Create a new SchemaLoader instance.
     *
     * @param  array<string>  $sources
     */
    public function __construct(
        array $sources,
        bool $useCache = true,
        int $cacheTtl = 3600,
        string $cachePrefix = 'schema',
        bool $dnsResolutionEnabled = true,
        int $httpTimeout = 10
    ) {
        $this->sources = $sources;
        $this->useCache = $useCache;
        $this->cacheTtl = $cacheTtl;
        $this->cachePrefix = $cachePrefix;
        $this->dnsResolutionEnabled = $dnsResolutionEnabled;
        $this->httpTimeout = $httpTimeout;
        $this->hasResolver = class_exists('SocialDept\\Resolver\\Resolver');
    }

    /**
     * Find schema by NSID (nullable version).
     */
    public function find(string $nsid): ?LexiconDocument
    {
        try {
            return $this->load($nsid);
        } catch (SchemaNotFoundException) {
            return null;
        }
    }

    /**
     * Load schema by NSID.
     */
    public function load(string $nsid): LexiconDocument
    {
        // Check memory cache first
        if (isset($this->memoryCache[$nsid])) {
            return $this->memoryCache[$nsid];
        }

        // Check Laravel cache
        if ($this->useCache) {
            $cacheKey = $this->getCacheKey($nsid);
            $cached = Cache::get($cacheKey);

            if ($cached !== null) {
                // Cache stores raw arrays, convert to LexiconDocument
                $document = LexiconDocument::fromArray($cached);
                $this->memoryCache[$nsid] = $document;

                return $document;
            }
        }

        // Load raw array data from sources
        $data = $this->loadFromSources($nsid);

        // Parse into LexiconDocument
        $document = LexiconDocument::fromArray($data);

        // Cache both in memory (as object) and Laravel cache (as array)
        $this->memoryCache[$nsid] = $document;

        if ($this->useCache) {
            Cache::put($this->getCacheKey($nsid), $data, $this->cacheTtl);
        }

        return $document;
    }

    /**
     * Load raw schema array by NSID.
     */
    protected function loadRaw(string $nsid): array
    {
        $document = $this->load($nsid);

        return $document->toArray();
    }

    /**
     * Get all available schema NSIDs.
     *
     * @return array<string>
     */
    public function all(): array
    {
        $nsids = [];

        // Scan all source directories for lexicon files
        foreach ($this->sources as $source) {
            if (! is_dir($source)) {
                continue;
            }

            // Recursively scan for .json files
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS)
            );

            foreach ($files as $file) {
                if ($file->isFile() && $file->getExtension() === 'json') {
                    // Try to parse the NSID from the file
                    try {
                        $contents = file_get_contents($file->getPathname());
                        $data = json_decode($contents, true);

                        if (isset($data['id'])) {
                            $nsids[] = $data['id'];
                        }
                    } catch (\Exception $e) {
                        // Skip invalid files
                        continue;
                    }
                }
            }
        }

        return array_unique($nsids);
    }

    /**
     * Check if schema exists.
     */
    public function exists(string $nsid): bool
    {
        try {
            $this->load($nsid);

            return true;
        } catch (SchemaNotFoundException) {
            return false;
        }
    }

    /**
     * Load schema from configured sources.
     */
    protected function loadFromSources(string $nsid): array
    {
        foreach ($this->sources as $source) {
            // Try to load from this source
            $schema = $this->loadFromSource($nsid, $source);

            if ($schema !== null) {
                return $schema;
            }
        }

        // Try DNS resolution as fallback if enabled
        if ($this->dnsResolutionEnabled) {
            $schema = $this->loadViaDns($nsid);

            if ($schema !== null) {
                return $schema;
            }
        }

        throw SchemaNotFoundException::forNsid($nsid);
    }

    /**
     * Load schema from a specific source directory.
     */
    protected function loadFromSource(string $nsid, string $source): ?array
    {
        // Try NSID-based path (app.bsky.feed.post -> app/bsky/feed/post.json)
        $nsidPath = $this->nsidToPath($nsid);
        $jsonPath = $source.'/'.$nsidPath.'.json';

        if (file_exists($jsonPath)) {
            return $this->loadJsonFile($jsonPath, $nsid);
        }

        // Try PHP file
        $phpPath = $source.'/'.$nsidPath.'.php';

        if (file_exists($phpPath)) {
            return $this->loadPhpFile($phpPath, $nsid);
        }

        // Try flat structure (app.bsky.feed.post.json)
        $flatJsonPath = $source.'/'.$nsid.'.json';

        if (file_exists($flatJsonPath)) {
            return $this->loadJsonFile($flatJsonPath, $nsid);
        }

        $flatPhpPath = $source.'/'.$nsid.'.php';

        if (file_exists($flatPhpPath)) {
            return $this->loadPhpFile($flatPhpPath, $nsid);
        }

        return null;
    }

    /**
     * Convert NSID to file path (app.bsky.feed.post -> app/bsky/feed/post).
     */
    protected function nsidToPath(string $nsid): string
    {
        return str_replace('.', '/', $nsid);
    }

    /**
     * Load and parse JSON file.
     */
    protected function loadJsonFile(string $path, string $nsid): array
    {
        $contents = file_get_contents($path);

        if ($contents === false) {
            throw SchemaNotFoundException::forFile($path);
        }

        $data = json_decode($contents, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw SchemaParseException::invalidJson($nsid, json_last_error_msg());
        }

        if (! is_array($data)) {
            throw SchemaParseException::malformed($nsid, 'Schema must be a JSON object');
        }

        return $data;
    }

    /**
     * Load PHP file returning array.
     */
    protected function loadPhpFile(string $path, string $nsid): array
    {
        $data = include $path;

        if (! is_array($data)) {
            throw SchemaParseException::malformed($nsid, 'PHP file must return an array');
        }

        return $data;
    }

    /**
     * Get cache key for NSID.
     */
    protected function getCacheKey(string $nsid): string
    {
        return "{$this->cachePrefix}:parsed:{$nsid}";
    }

    /**
     * Clear cached schema.
     */
    public function clearCache(?string $nsid = null): void
    {
        if ($nsid === null) {
            // Clear all memory cache
            $this->memoryCache = [];

            // Note: Can't easily clear all Laravel cache entries with prefix
            // Users should call Cache::flush() or use cache tags if needed
            return;
        }

        // Clear specific NSID from memory cache
        unset($this->memoryCache[$nsid]);

        // Clear from Laravel cache
        if ($this->useCache) {
            Cache::forget($this->getCacheKey($nsid));
        }
    }

    /**
     * Get all cached NSIDs from memory.
     *
     * @return array<string>
     */
    public function getCachedNsids(): array
    {
        return array_keys($this->memoryCache);
    }

    /**
     * Load schema via DNS resolution following AT Protocol spec.
     *
     * AT Protocol DNS-based lexicon discovery:
     * 1. Query DNS TXT record at _lexicon.<authority-domain>
     * 2. Extract DID from TXT record (format: did=<DID>)
     * 3. Resolve DID to PDS endpoint (requires atp-resolver package)
     * 4. Fetch lexicon from repository via com.atproto.repo.getRecord
     */
    protected function loadViaDns(string $nsid): ?array
    {
        // Check if atp-resolver is available
        if (! $this->hasResolver) {
            $this->showResolverWarning();

            return null;
        }

        try {
            $nsidParsed = Nsid::parse($nsid);

            // Step 1: Query DNS TXT record for DID
            $did = $this->queryLexiconDid($nsidParsed);
            if ($did === null) {
                return null;
            }

            // Step 2: Resolve DID to PDS endpoint
            $pdsUrl = $this->resolvePdsEndpoint($did);
            if ($pdsUrl === null) {
                return null;
            }

            // Step 3: Fetch lexicon schema from repository
            return $this->fetchLexiconFromRepository($pdsUrl, $did, $nsid);
        } catch (\Exception $e) {
            // Silently fail and return null - will try other sources or fail with main error
            return null;
        }
    }

    /**
     * Query DNS TXT record for lexicon DID.
     *
     * Queries _lexicon.<authority-domain> for TXT record containing did=<DID>
     */
    protected function queryLexiconDid(Nsid $nsid): ?string
    {
        // Convert authority to domain (e.g., pub.leaflet -> leaflet.pub)
        $authority = $nsid->getAuthority();
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
     * Fetch lexicon schema from AT Protocol repository.
     */
    protected function fetchLexiconFromRepository(string $pdsUrl, string $did, string $nsid): ?array
    {
        try {
            // Construct XRPC call to com.atproto.repo.getRecord
            $response = Http::timeout($this->httpTimeout)
                ->get("{$pdsUrl}/xrpc/com.atproto.repo.getRecord", [
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
            return null;
        }

        return null;
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
