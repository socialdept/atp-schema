<?php

namespace SocialDept\AtpSchema\Tests\Unit\Data;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Data\LexiconDocument;
use SocialDept\AtpSchema\Exceptions\SchemaValidationException;
use SocialDept\AtpSchema\Parser\Nsid;

class LexiconDocumentTest extends TestCase
{
    public function test_it_creates_from_array(): void
    {
        $data = [
            'lexicon' => 1,
            'id' => 'app.bsky.feed.post',
            'description' => 'A post record',
            'defs' => [
                'main' => [
                    'type' => 'record',
                ],
            ],
        ];

        $doc = LexiconDocument::fromArray($data, 'test.json');

        $this->assertSame(1, $doc->lexicon);
        $this->assertInstanceOf(Nsid::class, $doc->id);
        $this->assertSame('app.bsky.feed.post', $doc->id->toString());
        $this->assertSame('A post record', $doc->description);
        $this->assertArrayHasKey('main', $doc->defs);
        $this->assertSame('test.json', $doc->source);
        $this->assertSame($data, $doc->raw);
    }

    public function test_it_creates_from_json(): void
    {
        $json = json_encode([
            'lexicon' => 1,
            'id' => 'app.bsky.feed.post',
            'description' => 'A post record',
            'defs' => [
                'main' => [
                    'type' => 'record',
                ],
            ],
        ]);

        $doc = LexiconDocument::fromJson($json, 'test.json');

        $this->assertSame(1, $doc->lexicon);
        $this->assertInstanceOf(Nsid::class, $doc->id);
        $this->assertSame('app.bsky.feed.post', $doc->id->toString());
        $this->assertSame('A post record', $doc->description);
        $this->assertArrayHasKey('main', $doc->defs);
        $this->assertSame('test.json', $doc->source);
    }

    public function test_it_creates_from_json_without_source(): void
    {
        $json = json_encode([
            'lexicon' => 1,
            'id' => 'app.bsky.feed.post',
            'defs' => [
                'main' => ['type' => 'record'],
            ],
        ]);

        $doc = LexiconDocument::fromJson($json);

        $this->assertSame(1, $doc->lexicon);
        $this->assertSame('app.bsky.feed.post', $doc->id->toString());
        $this->assertNull($doc->source);
    }

    public function test_it_throws_on_invalid_json(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JSON');

        LexiconDocument::fromJson('{"invalid json');
    }

    public function test_it_throws_on_non_array_json(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('JSON must decode to an array');

        LexiconDocument::fromJson('"just a string"');
    }

    public function test_it_throws_on_json_array_list(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('JSON must decode to an array');

        LexiconDocument::fromJson('123');
    }

    public function test_it_throws_on_missing_lexicon(): void
    {
        $this->expectException(SchemaValidationException::class);
        $this->expectExceptionMessage('Required field missing in schema unknown: lexicon');

        LexiconDocument::fromArray([
            'id' => 'app.bsky.feed.post',
            'defs' => [],
        ]);
    }

    public function test_it_throws_on_missing_id(): void
    {
        $this->expectException(SchemaValidationException::class);
        $this->expectExceptionMessage('Required field missing in schema unknown: id');

        LexiconDocument::fromArray([
            'lexicon' => 1,
            'defs' => [],
        ]);
    }

    public function test_it_throws_on_missing_defs(): void
    {
        $this->expectException(SchemaValidationException::class);
        $this->expectExceptionMessage('Required field missing in schema app.bsky.feed.post: defs');

        LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'app.bsky.feed.post',
        ]);
    }

    public function test_it_throws_on_invalid_lexicon_version(): void
    {
        $this->expectException(SchemaValidationException::class);
        $this->expectExceptionMessage('Unsupported lexicon version 2 in schema app.bsky.feed.post');

        LexiconDocument::fromArray([
            'lexicon' => 2,
            'id' => 'app.bsky.feed.post',
            'defs' => [],
        ]);
    }

    public function test_it_gets_definition(): void
    {
        $doc = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'app.bsky.feed.post',
            'defs' => [
                'main' => ['type' => 'record'],
                'other' => ['type' => 'object'],
            ],
        ]);

        $this->assertSame(['type' => 'record'], $doc->getDefinition('main'));
        $this->assertSame(['type' => 'object'], $doc->getDefinition('other'));
        $this->assertNull($doc->getDefinition('nonexistent'));
    }

    public function test_it_checks_definition_exists(): void
    {
        $doc = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'app.bsky.feed.post',
            'defs' => [
                'main' => ['type' => 'record'],
            ],
        ]);

        $this->assertTrue($doc->hasDefinition('main'));
        $this->assertFalse($doc->hasDefinition('nonexistent'));
    }

    public function test_it_gets_main_definition(): void
    {
        $doc = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'app.bsky.feed.post',
            'defs' => [
                'main' => ['type' => 'record'],
            ],
        ]);

        $this->assertSame(['type' => 'record'], $doc->getMainDefinition());
    }

    public function test_it_gets_definition_names(): void
    {
        $doc = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'app.bsky.feed.post',
            'defs' => [
                'main' => ['type' => 'record'],
                'other' => ['type' => 'object'],
                'another' => ['type' => 'string'],
            ],
        ]);

        $names = $doc->getDefinitionNames();

        $this->assertCount(3, $names);
        $this->assertContains('main', $names);
        $this->assertContains('other', $names);
        $this->assertContains('another', $names);
    }

    public function test_it_gets_nsid(): void
    {
        $doc = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'app.bsky.feed.post',
            'defs' => [],
        ]);

        $this->assertSame('app.bsky.feed.post', $doc->getNsid());
    }

    public function test_it_gets_version(): void
    {
        $doc = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'app.bsky.feed.post',
            'defs' => [],
        ]);

        $this->assertSame(1, $doc->getVersion());
    }

    public function test_it_gets_version_same_as_lexicon_property(): void
    {
        $doc = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'app.bsky.feed.post',
            'defs' => [],
        ]);

        $this->assertSame($doc->lexicon, $doc->getVersion());
    }

    public function test_it_converts_to_array(): void
    {
        $doc = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'app.bsky.feed.post',
            'description' => 'A post record',
            'defs' => [
                'main' => ['type' => 'record'],
            ],
        ]);

        $array = $doc->toArray();

        $this->assertSame(1, $array['lexicon']);
        $this->assertSame('app.bsky.feed.post', $array['id']);
        $this->assertSame('A post record', $array['description']);
        $this->assertArrayHasKey('main', $array['defs']);
    }

    public function test_it_identifies_record_schema(): void
    {
        $doc = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'app.bsky.feed.post',
            'defs' => [
                'main' => ['type' => 'record'],
            ],
        ]);

        $this->assertTrue($doc->isRecord());
        $this->assertFalse($doc->isQuery());
        $this->assertFalse($doc->isProcedure());
        $this->assertFalse($doc->isSubscription());
    }

    public function test_it_identifies_query_schema(): void
    {
        $doc = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'com.atproto.repo.getRecord',
            'defs' => [
                'main' => ['type' => 'query'],
            ],
        ]);

        $this->assertTrue($doc->isQuery());
        $this->assertFalse($doc->isRecord());
        $this->assertFalse($doc->isProcedure());
        $this->assertFalse($doc->isSubscription());
    }

    public function test_it_identifies_procedure_schema(): void
    {
        $doc = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'com.atproto.repo.createRecord',
            'defs' => [
                'main' => ['type' => 'procedure'],
            ],
        ]);

        $this->assertTrue($doc->isProcedure());
        $this->assertFalse($doc->isRecord());
        $this->assertFalse($doc->isQuery());
        $this->assertFalse($doc->isSubscription());
    }

    public function test_it_identifies_subscription_schema(): void
    {
        $doc = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'com.atproto.sync.subscribeRepos',
            'defs' => [
                'main' => ['type' => 'subscription'],
            ],
        ]);

        $this->assertTrue($doc->isSubscription());
        $this->assertFalse($doc->isRecord());
        $this->assertFalse($doc->isQuery());
        $this->assertFalse($doc->isProcedure());
    }
}
