<?php

namespace SocialDept\Schema\Tests\Unit\Parser;

use Orchestra\Testbench\TestCase;
use SocialDept\Schema\Data\LexiconDocument;
use SocialDept\Schema\Data\Types\ObjectType;
use SocialDept\Schema\Data\Types\StringType;
use SocialDept\Schema\Exceptions\TypeResolutionException;
use SocialDept\Schema\Parser\TypeParser;

class TypeParserTest extends TestCase
{
    protected TypeParser $parser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = new TypeParser();
    }

    public function test_it_parses_primitive_types(): void
    {
        $type = $this->parser->parse(['type' => 'string']);

        $this->assertInstanceOf(StringType::class, $type);
    }

    public function test_it_parses_complex_types(): void
    {
        $type = $this->parser->parse(['type' => 'object']);

        $this->assertInstanceOf(ObjectType::class, $type);
    }

    public function test_it_throws_on_missing_type(): void
    {
        $this->expectException(TypeResolutionException::class);
        $this->expectExceptionMessage('Unknown Lexicon type: (missing type field)');

        $this->parser->parse([]);
    }

    public function test_it_throws_on_unknown_type(): void
    {
        $this->expectException(TypeResolutionException::class);
        $this->expectExceptionMessage('Unknown Lexicon type: nonexistent');

        $this->parser->parse(['type' => 'nonexistent']);
    }

    public function test_it_resolves_local_reference(): void
    {
        $document = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'com.example.test',
            'defs' => [
                'main' => ['type' => 'object'],
                'other' => ['type' => 'string'],
            ],
        ]);

        $type = $this->parser->resolveReference('#other', $document);

        $this->assertInstanceOf(StringType::class, $type);
    }

    public function test_it_throws_on_unresolvable_local_reference(): void
    {
        $document = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'com.example.test',
            'defs' => [
                'main' => ['type' => 'object'],
            ],
        ]);

        $this->expectException(TypeResolutionException::class);
        $this->expectExceptionMessage('Cannot resolve reference #nonexistent in schema com.example.test');

        $this->parser->resolveReference('#nonexistent', $document);
    }

    public function test_it_caches_resolved_types(): void
    {
        $document = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'com.example.test',
            'defs' => [
                'main' => ['type' => 'object'],
                'other' => ['type' => 'string'],
            ],
        ]);

        $type1 = $this->parser->resolveReference('#other', $document);
        $type2 = $this->parser->resolveReference('#other', $document);

        $this->assertSame($type1, $type2);
        $this->assertCount(1, $this->parser->getResolvedTypes());
    }

    public function test_it_clears_cache(): void
    {
        $document = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'com.example.test',
            'defs' => [
                'main' => ['type' => 'object'],
                'other' => ['type' => 'string'],
            ],
        ]);

        $this->parser->resolveReference('#other', $document);
        $this->assertCount(1, $this->parser->getResolvedTypes());

        $this->parser->clearCache();
        $this->assertCount(0, $this->parser->getResolvedTypes());
    }

    public function test_it_throws_on_external_reference_without_loader(): void
    {
        $document = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'com.example.test',
            'defs' => [
                'main' => ['type' => 'object'],
            ],
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cannot resolve external reference without SchemaLoader');

        $this->parser->resolveReference('com.atproto.repo.getRecord', $document);
    }
}
