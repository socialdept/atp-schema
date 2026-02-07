<?php

namespace SocialDept\AtpSchema\Parser;

use Illuminate\Support\Facades\Cache;
use SocialDept\AtpSchema\Contracts\SchemaRepository;
use SocialDept\AtpSchema\Data\LexiconDocument;
use SocialDept\AtpSchema\Exceptions\SchemaNotFoundException;
use SocialDept\AtpSchema\Exceptions\SchemaParseException;
use SocialDept\AtpSupport\Resolver;
use SocialDept\AtpSupport\Resolvers\LexiconDnsResolver;

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
     * Delegates to LexiconDnsResolver from atp-support which handles:
     * 1. Query DNS TXT record at _lexicon.<authority-domain>
     * 2. Extract DID from TXT record (format: did=<DID>)
     * 3. Resolve DID to PDS endpoint
     * 4. Fetch lexicon from repository via com.atproto.repo.getRecord
     */
    protected function loadViaDns(string $nsid): ?array
    {
        try {
            $resolver = app(Resolver::class);
            $dnsResolver = new LexiconDnsResolver($resolver, $this->httpTimeout);

            return $dnsResolver->resolve($nsid);
        } catch (\Exception $e) {
            return null;
        }
    }
}
