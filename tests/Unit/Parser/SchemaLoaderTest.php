<?php

namespace SocialDept\Schema\Tests\Unit\Parser;

use Illuminate\Support\Facades\Cache;
use Orchestra\Testbench\TestCase;
use SocialDept\Schema\Exceptions\SchemaNotFoundException;
use SocialDept\Schema\Exceptions\SchemaParseException;
use SocialDept\Schema\Parser\SchemaLoader;

class SchemaLoaderTest extends TestCase
{
    protected string $fixturesPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixturesPath = __DIR__ . '/../../fixtures';

        // Clear cache before each test
        Cache::flush();
    }

    public function test_it_loads_schema_from_hierarchical_json(): void
    {
        $loader = new SchemaLoader([$this->fixturesPath], false);

        $schema = $loader->load('app.bsky.feed.post');

        $this->assertIsArray($schema);
        $this->assertSame(1, $schema['lexicon']);
        $this->assertSame('app.bsky.feed.post', $schema['id']);
    }

    public function test_it_loads_schema_from_flat_php(): void
    {
        $loader = new SchemaLoader([$this->fixturesPath], false);

        $schema = $loader->load('com.atproto.repo.getRecord');

        $this->assertIsArray($schema);
        $this->assertSame(1, $schema['lexicon']);
        $this->assertSame('com.atproto.repo.getRecord', $schema['id']);
    }

    public function test_it_checks_if_schema_exists(): void
    {
        $loader = new SchemaLoader([$this->fixturesPath], false);

        $this->assertTrue($loader->exists('app.bsky.feed.post'));
        $this->assertTrue($loader->exists('com.atproto.repo.getRecord'));
        $this->assertFalse($loader->exists('nonexistent.schema'));
    }

    public function test_it_throws_when_schema_not_found(): void
    {
        $loader = new SchemaLoader([$this->fixturesPath], false);

        $this->expectException(SchemaNotFoundException::class);
        $this->expectExceptionMessage('Schema not found for NSID: nonexistent.schema');

        $loader->load('nonexistent.schema');
    }

    public function test_it_caches_schemas_in_memory(): void
    {
        $loader = new SchemaLoader([$this->fixturesPath], false);

        // First load
        $schema1 = $loader->load('app.bsky.feed.post');

        // Second load should come from memory
        $schema2 = $loader->load('app.bsky.feed.post');

        $this->assertSame($schema1, $schema2);
        $this->assertContains('app.bsky.feed.post', $loader->getCachedNsids());
    }

    public function test_it_caches_schemas_in_laravel_cache(): void
    {
        $loader = new SchemaLoader([$this->fixturesPath], true, 3600, 'schema');

        // First load - should store in cache
        $schema = $loader->load('app.bsky.feed.post');

        // Clear memory cache to force Laravel cache lookup
        $loader->clearCache('app.bsky.feed.post');

        // Manually put it back in Laravel cache
        Cache::put('schema:parsed:app.bsky.feed.post', $schema, 3600);

        // This should retrieve from Laravel cache
        $cached = $loader->load('app.bsky.feed.post');

        $this->assertSame($schema, $cached);
    }

    public function test_it_retrieves_from_laravel_cache(): void
    {
        $loader = new SchemaLoader([$this->fixturesPath], true);

        // First load to cache it
        $originalSchema = $loader->load('app.bsky.feed.post');

        // Clear memory cache
        $loader->clearCache('app.bsky.feed.post');

        // Second load should come from Laravel cache
        $cachedSchema = $loader->load('app.bsky.feed.post');

        $this->assertSame($originalSchema, $cachedSchema);
        $this->assertSame('app.bsky.feed.post', $cachedSchema['id']);
    }

    public function test_it_clears_specific_schema_cache(): void
    {
        $loader = new SchemaLoader([$this->fixturesPath], true);

        // Load to populate caches
        $loader->load('app.bsky.feed.post');

        $this->assertContains('app.bsky.feed.post', $loader->getCachedNsids());

        // Clear cache
        $loader->clearCache('app.bsky.feed.post');

        // Memory cache should be cleared
        $this->assertNotContains('app.bsky.feed.post', $loader->getCachedNsids());

        // Laravel cache should also be cleared (verify by loading again and checking it comes from file)
        $this->assertFalse(Cache::has('schema:parsed:app.bsky.feed.post'));
    }

    public function test_it_clears_all_memory_cache(): void
    {
        $loader = new SchemaLoader([$this->fixturesPath], false);

        // Load multiple schemas
        $loader->load('app.bsky.feed.post');
        $loader->load('com.atproto.repo.getRecord');

        $this->assertCount(2, $loader->getCachedNsids());

        // Clear all
        $loader->clearCache();

        $this->assertCount(0, $loader->getCachedNsids());
    }

    public function test_it_searches_multiple_sources_in_order(): void
    {
        $source1 = $this->fixturesPath . '/source1';
        $source2 = $this->fixturesPath;

        // Schema only exists in source2
        $loader = new SchemaLoader([$source1, $source2], false);

        $schema = $loader->load('app.bsky.feed.post');

        $this->assertSame('app.bsky.feed.post', $schema['id']);
    }

    public function test_it_throws_on_invalid_json(): void
    {
        $invalidPath = $this->fixturesPath . '/invalid';
        @mkdir($invalidPath, 0755, true);
        file_put_contents($invalidPath . '/invalid.json', '{invalid json}');

        $loader = new SchemaLoader([$invalidPath], false);

        $this->expectException(SchemaParseException::class);
        $this->expectExceptionMessage('Failed to parse schema JSON');

        try {
            $loader->load('invalid');
        } finally {
            @unlink($invalidPath . '/invalid.json');
            @rmdir($invalidPath);
        }
    }

    public function test_it_throws_on_php_file_not_returning_array(): void
    {
        $invalidPath = $this->fixturesPath . '/invalid';
        @mkdir($invalidPath, 0755, true);
        file_put_contents($invalidPath . '/invalid.php', '<?php return "not an array";');

        $loader = new SchemaLoader([$invalidPath], false);

        $this->expectException(SchemaParseException::class);
        $this->expectExceptionMessage('PHP file must return an array');

        try {
            $loader->load('invalid');
        } finally {
            @unlink($invalidPath . '/invalid.php');
            @rmdir($invalidPath);
        }
    }

}
