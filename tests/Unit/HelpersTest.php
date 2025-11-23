<?php

namespace SocialDept\Schema\Tests\Unit;

use Orchestra\Testbench\TestCase;
use SocialDept\Schema\Data\LexiconDocument;
use SocialDept\Schema\SchemaManager;
use SocialDept\Schema\SchemaServiceProvider;

class HelpersTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [SchemaServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('schema.sources', [__DIR__.'/../fixtures']);
        $app['config']->set('schema.cache.enabled', false);
    }

    public function test_schema_helper_returns_manager(): void
    {
        $manager = schema();

        $this->assertInstanceOf(SchemaManager::class, $manager);
    }

    public function test_schema_helper_loads_schema(): void
    {
        $schema = schema('app.bsky.feed.post');

        $this->assertInstanceOf(LexiconDocument::class, $schema);
        $this->assertSame('app.bsky.feed.post', $schema->getNsid());
    }

    public function test_schema_validate_helper_validates_data(): void
    {
        $validData = [
            'text' => 'Hello, world!',
            'createdAt' => '2024-01-01T12:00:00Z',
        ];

        $result = schema_validate('app.bsky.feed.post', $validData);

        $this->assertTrue($result);
    }

    public function test_schema_validate_helper_returns_false_for_invalid_data(): void
    {
        $invalidData = [
            'text' => '', // Required field
        ];

        $result = schema_validate('app.bsky.feed.post', $invalidData);

        $this->assertFalse($result);
    }

    public function test_schema_parse_helper_parses_schema(): void
    {
        $document = schema('app.bsky.feed.post');

        $this->assertInstanceOf(LexiconDocument::class, $document);
        $this->assertSame('app.bsky.feed.post', $document->getNsid());
    }

    public function test_schema_generate_helper_generates_code(): void
    {
        $code = schema()->generate('app.bsky.feed.post');

        $this->assertIsString($code);
        $this->assertStringContainsString('namespace', $code);
        $this->assertStringContainsString('class', $code);
    }

    public function test_schema_generate_helper_accepts_options(): void
    {
        $code = schema()->generate('app.bsky.feed.post');

        $this->assertIsString($code);
        $this->assertStringContainsString('namespace', $code);
    }
}
