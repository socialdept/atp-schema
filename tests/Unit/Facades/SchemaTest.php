<?php

namespace SocialDept\AtpSchema\Tests\Unit\Facades;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Data\LexiconDocument;
use SocialDept\AtpSchema\Facades\Schema;
use SocialDept\AtpSchema\SchemaServiceProvider;

class SchemaTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [SchemaServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('schema.sources', [__DIR__.'/../../fixtures']);
        $app['config']->set('schema.cache.enabled', false);
    }

    public function test_it_loads_schema(): void
    {
        $schema = Schema::load('app.bsky.feed.post');

        $this->assertInstanceOf(LexiconDocument::class, $schema);
        $this->assertSame('app.bsky.feed.post', $schema->getNsid());
    }

    public function test_it_checks_if_schema_exists(): void
    {
        $this->assertTrue(Schema::exists('app.bsky.feed.post'));
        $this->assertFalse(Schema::exists('nonexistent.schema'));
    }

    public function test_it_parses_schema(): void
    {
        // parse() is an alias for load()
        $document = Schema::load('app.bsky.feed.post');

        $this->assertInstanceOf(LexiconDocument::class, $document);
        $this->assertSame('app.bsky.feed.post', $document->getNsid());
    }

    public function test_it_validates_data(): void
    {
        $validData = [
            'text' => 'Hello, world!',
            'createdAt' => '2024-01-01T12:00:00Z',
        ];

        $result = Schema::validate('app.bsky.feed.post', $validData);

        $this->assertTrue($result);
    }

    public function test_it_validates_with_errors(): void
    {
        $invalidData = [
            'text' => '', // Required field
        ];

        $errors = Schema::validateWithErrors('app.bsky.feed.post', $invalidData);

        $this->assertIsArray($errors);
        $this->assertNotEmpty($errors);
    }

    public function test_it_generates_code(): void
    {
        $code = Schema::generate('app.bsky.feed.post');

        $this->assertIsString($code);
        $this->assertStringContainsString('namespace', $code);
        $this->assertStringContainsString('class', $code);
    }

    public function test_it_clears_cache_for_specific_nsid(): void
    {
        // Load to cache
        Schema::load('app.bsky.feed.post');

        // Clear cache should not throw
        Schema::clearCache('app.bsky.feed.post');

        // Should still be able to load after clearing
        $schema = Schema::load('app.bsky.feed.post');
        $this->assertSame('app.bsky.feed.post', $schema->getNsid());
    }

    public function test_it_clears_all_cache(): void
    {
        // Load multiple schemas
        Schema::load('app.bsky.feed.post');
        Schema::load('com.atproto.repo.getRecord');

        // Clear all cache should not throw
        Schema::clearCache();

        // Should still be able to load after clearing
        $schema1 = Schema::load('app.bsky.feed.post');
        $schema2 = Schema::load('com.atproto.repo.getRecord');

        $this->assertSame('app.bsky.feed.post', $schema1->getNsid());
        $this->assertSame('com.atproto.repo.getRecord', $schema2->getNsid());
    }
}
