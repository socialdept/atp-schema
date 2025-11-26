<?php

namespace SocialDept\AtpSchema\Tests\Unit;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Data\LexiconDocument;
use SocialDept\AtpSchema\Generator\DTOGenerator;
use SocialDept\AtpSchema\Parser\SchemaLoader;
use SocialDept\AtpSchema\SchemaManager;
use SocialDept\AtpSchema\Validation\LexiconValidator;

class SchemaManagerTest extends TestCase
{
    protected string $fixturesPath;

    protected SchemaLoader $loader;

    protected LexiconValidator $validator;

    protected DTOGenerator $generator;

    protected SchemaManager $manager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixturesPath = __DIR__.'/../fixtures';

        // Create mock dependencies
        $this->loader = new SchemaLoader([$this->fixturesPath], false);
        $this->validator = new LexiconValidator($this->loader);
        $this->generator = new DTOGenerator($this->loader, 'App\\Lexicon', 'app/Lexicon');

        // Create manager
        $this->manager = new SchemaManager(
            $this->loader,
            $this->validator,
            $this->generator
        );
    }

    public function test_it_loads_schema(): void
    {
        $schema = $this->manager->load('app.bsky.feed.post');

        $this->assertInstanceOf(LexiconDocument::class, $schema);
        $this->assertSame('app.bsky.feed.post', $schema->getNsid());
    }

    public function test_it_checks_if_schema_exists(): void
    {
        $this->assertTrue($this->manager->exists('app.bsky.feed.post'));
        $this->assertFalse($this->manager->exists('nonexistent.schema'));
    }

    public function test_it_parses_schema_into_document(): void
    {
        $document = $this->manager->load('app.bsky.feed.post');

        $this->assertInstanceOf(LexiconDocument::class, $document);
        $this->assertSame('app.bsky.feed.post', $document->getNsid());
        $this->assertSame(1, $document->lexicon);
    }

    public function test_it_validates_data_against_schema(): void
    {
        $validData = [
            'text' => 'Hello, world!',
            'createdAt' => '2024-01-01T12:00:00Z',
        ];

        $result = $this->manager->validate('app.bsky.feed.post', $validData);

        $this->assertTrue($result);
    }

    public function test_it_validates_data_with_errors(): void
    {
        $invalidData = [
            'text' => '', // Required field
        ];

        $errors = $this->manager->validateWithErrors('app.bsky.feed.post', $invalidData);

        $this->assertIsArray($errors);
        $this->assertNotEmpty($errors);
    }

    public function test_it_generates_dto_code(): void
    {
        $code = $this->manager->generate('app.bsky.feed.post');

        $this->assertIsString($code);
        $this->assertStringContainsString('namespace', $code);
        $this->assertStringContainsString('class', $code);
    }

    public function test_it_throws_when_generator_not_available(): void
    {
        $managerWithoutGenerator = new SchemaManager(
            $this->loader,
            $this->validator,
            null
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Generator not available');

        $managerWithoutGenerator->generate('app.bsky.feed.post');
    }

    public function test_it_clears_cache_for_specific_nsid(): void
    {
        // Load to cache
        $this->manager->load('app.bsky.feed.post');

        $this->assertContains('app.bsky.feed.post', $this->loader->getCachedNsids());

        // Clear cache
        $this->manager->clearCache('app.bsky.feed.post');

        $this->assertNotContains('app.bsky.feed.post', $this->loader->getCachedNsids());
    }

    public function test_it_clears_all_cache(): void
    {
        // Load multiple schemas
        $this->manager->load('app.bsky.feed.post');
        $this->manager->load('com.atproto.repo.getRecord');

        $this->assertCount(2, $this->loader->getCachedNsids());

        // Clear all
        $this->manager->clearCache();

        $this->assertCount(0, $this->loader->getCachedNsids());
    }

    public function test_it_gets_loader(): void
    {
        $loader = $this->manager->getLoader();

        $this->assertSame($this->loader, $loader);
    }

    public function test_it_gets_validator(): void
    {
        $validator = $this->manager->getValidator();

        $this->assertSame($this->validator, $validator);
    }

    public function test_it_gets_generator(): void
    {
        $generator = $this->manager->getGenerator();

        $this->assertSame($this->generator, $generator);
    }

    public function test_it_sets_generator(): void
    {
        $newGenerator = new DTOGenerator($this->loader, 'Custom\\Namespace', 'custom/path');

        $this->manager->setGenerator($newGenerator);

        $this->assertSame($newGenerator, $this->manager->getGenerator());
    }

    public function test_it_gets_null_generator_when_not_set(): void
    {
        $managerWithoutGenerator = new SchemaManager(
            $this->loader,
            $this->validator,
            null
        );

        $this->assertNull($managerWithoutGenerator->getGenerator());
    }
}
