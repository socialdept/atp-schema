<?php

namespace SocialDept\Schema\Parser;

use Illuminate\Support\Facades\Cache;
use SocialDept\Schema\Exceptions\SchemaNotFoundException;
use SocialDept\Schema\Exceptions\SchemaParseException;

class SchemaLoader
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
     * Create a new SchemaLoader instance.
     *
     * @param  array<string>  $sources
     */
    public function __construct(
        array $sources,
        bool $useCache = true,
        int $cacheTtl = 3600,
        string $cachePrefix = 'schema'
    ) {
        $this->sources = $sources;
        $this->useCache = $useCache;
        $this->cacheTtl = $cacheTtl;
        $this->cachePrefix = $cachePrefix;
    }

    /**
     * Load schema by NSID.
     */
    public function load(string $nsid): array
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
                $this->memoryCache[$nsid] = $cached;

                return $cached;
            }
        }

        // Load from sources
        $schema = $this->loadFromSources($nsid);

        // Cache the result
        $this->memoryCache[$nsid] = $schema;

        if ($this->useCache) {
            Cache::put($this->getCacheKey($nsid), $schema, $this->cacheTtl);
        }

        return $schema;
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

        throw SchemaNotFoundException::forNsid($nsid);
    }

    /**
     * Load schema from a specific source directory.
     */
    protected function loadFromSource(string $nsid, string $source): ?array
    {
        // Try NSID-based path (app.bsky.feed.post -> app/bsky/feed/post.json)
        $nsidPath = $this->nsidToPath($nsid);
        $jsonPath = $source . '/' . $nsidPath . '.json';

        if (file_exists($jsonPath)) {
            return $this->loadJsonFile($jsonPath, $nsid);
        }

        // Try PHP file
        $phpPath = $source . '/' . $nsidPath . '.php';

        if (file_exists($phpPath)) {
            return $this->loadPhpFile($phpPath, $nsid);
        }

        // Try flat structure (app.bsky.feed.post.json)
        $flatJsonPath = $source . '/' . $nsid . '.json';

        if (file_exists($flatJsonPath)) {
            return $this->loadJsonFile($flatJsonPath, $nsid);
        }

        $flatPhpPath = $source . '/' . $nsid . '.php';

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
}
