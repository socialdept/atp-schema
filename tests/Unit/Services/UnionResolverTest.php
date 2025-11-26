<?php

namespace SocialDept\AtpSchema\Tests\Unit\Services;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Contracts\LexiconRegistry;
use SocialDept\AtpSchema\Data\LexiconDocument;
use SocialDept\AtpSchema\Exceptions\RecordValidationException;
use SocialDept\AtpSchema\Parser\Nsid;
use SocialDept\AtpSchema\Services\UnionResolver;

class UnionResolverTest extends TestCase
{
    protected UnionResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = new UnionResolver();
    }

    public function test_it_resolves_discriminated_union(): void
    {
        $data = ['$type' => 'app.bsky.feed.post'];

        $unionDef = [
            'type' => 'union',
            'refs' => ['app.bsky.feed.post', 'app.bsky.feed.repost'],
            'closed' => true,
        ];

        $type = $this->resolver->resolve($data, $unionDef);

        $this->assertEquals('app.bsky.feed.post', $type);
    }

    public function test_it_returns_null_for_open_union(): void
    {
        $data = ['text' => 'Hello'];

        $unionDef = [
            'type' => 'union',
            'refs' => ['app.bsky.feed.post'],
            'closed' => false,
        ];

        $type = $this->resolver->resolve($data, $unionDef);

        $this->assertNull($type);
    }

    public function test_it_throws_exception_for_discriminated_union_without_type(): void
    {
        $this->expectException(RecordValidationException::class);

        $data = ['text' => 'Hello'];

        $unionDef = [
            'type' => 'union',
            'refs' => ['app.bsky.feed.post'],
            'closed' => true,
        ];

        $this->resolver->resolve($data, $unionDef);
    }

    public function test_it_throws_exception_for_invalid_type(): void
    {
        $this->expectException(RecordValidationException::class);

        $data = ['$type' => 'app.bsky.feed.invalid'];

        $unionDef = [
            'type' => 'union',
            'refs' => ['app.bsky.feed.post', 'app.bsky.feed.repost'],
            'closed' => true,
        ];

        $this->resolver->resolve($data, $unionDef);
    }

    public function test_it_throws_exception_for_non_object_discriminated_union(): void
    {
        $this->expectException(RecordValidationException::class);

        $unionDef = [
            'type' => 'union',
            'refs' => ['app.bsky.feed.post'],
            'closed' => true,
        ];

        $this->resolver->resolve('not an object', $unionDef);
    }

    public function test_it_checks_if_data_matches_type(): void
    {
        $data = ['$type' => 'app.bsky.feed.post'];

        $unionDef = [
            'type' => 'union',
            'refs' => ['app.bsky.feed.post', 'app.bsky.feed.repost'],
            'closed' => true,
        ];

        $this->assertTrue($this->resolver->matches($data, 'app.bsky.feed.post', $unionDef));
        $this->assertFalse($this->resolver->matches($data, 'app.bsky.feed.repost', $unionDef));
    }

    public function test_it_returns_false_for_invalid_data_when_checking_match(): void
    {
        $data = ['text' => 'Hello'];

        $unionDef = [
            'type' => 'union',
            'refs' => ['app.bsky.feed.post'],
            'closed' => true,
        ];

        $this->assertFalse($this->resolver->matches($data, 'app.bsky.feed.post', $unionDef));
    }

    public function test_it_returns_false_for_open_union_when_checking_match(): void
    {
        $data = ['$type' => 'app.bsky.feed.post'];

        $unionDef = [
            'type' => 'union',
            'refs' => ['app.bsky.feed.post'],
            'closed' => false,
        ];

        $this->assertFalse($this->resolver->matches($data, 'app.bsky.feed.post', $unionDef));
    }

    public function test_it_gets_type_definition_with_registry(): void
    {
        // Create a simple registry implementation
        $registry = new class () implements LexiconRegistry {
            public function register(LexiconDocument $document): void
            {
            }

            public function get(string $nsid): ?LexiconDocument
            {
                return new LexiconDocument(
                    1,
                    Nsid::parse('app.bsky.feed.post'),
                    ['main' => ['type' => 'record']]
                );
            }

            public function has(string $nsid): bool
            {
                return true;
            }

            public function all(): array
            {
                return [];
            }

            public function clear(): void
            {
            }
        };

        $this->resolver->setRegistry($registry);

        $data = ['$type' => 'app.bsky.feed.post'];

        $unionDef = [
            'type' => 'union',
            'refs' => ['app.bsky.feed.post'],
            'closed' => true,
        ];

        $result = $this->resolver->getTypeDefinition($data, $unionDef);

        $this->assertInstanceOf(LexiconDocument::class, $result);
        $this->assertEquals('app.bsky.feed.post', $result->getNsid());
    }

    public function test_it_returns_null_for_type_definition_without_registry(): void
    {
        $data = ['$type' => 'app.bsky.feed.post'];

        $unionDef = [
            'type' => 'union',
            'refs' => ['app.bsky.feed.post'],
            'closed' => true,
        ];

        $result = $this->resolver->getTypeDefinition($data, $unionDef);

        $this->assertNull($result);
    }

    public function test_it_returns_null_for_type_definition_with_open_union(): void
    {
        // Create a simple registry implementation
        $registry = new class () implements LexiconRegistry {
            public function register(LexiconDocument $document): void
            {
            }

            public function get(string $nsid): ?LexiconDocument
            {
                return null;
            }

            public function has(string $nsid): bool
            {
                return false;
            }

            public function all(): array
            {
                return [];
            }

            public function clear(): void
            {
            }
        };

        $this->resolver->setRegistry($registry);

        $data = ['text' => 'Hello'];

        $unionDef = [
            'type' => 'union',
            'refs' => ['app.bsky.feed.post'],
            'closed' => false,
        ];

        $result = $this->resolver->getTypeDefinition($data, $unionDef);

        $this->assertNull($result);
    }

    public function test_it_validates_discriminated_union(): void
    {
        $data = ['$type' => 'app.bsky.feed.post'];
        $refs = ['app.bsky.feed.post', 'app.bsky.feed.repost'];

        $this->resolver->validateDiscriminated($data, $refs);

        $this->assertTrue(true); // No exception thrown
    }

    public function test_it_throws_exception_when_validating_non_object(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->resolver->validateDiscriminated('not an object', ['app.bsky.feed.post']);
    }

    public function test_it_throws_exception_when_validating_without_type(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->resolver->validateDiscriminated(['text' => 'Hello'], ['app.bsky.feed.post']);
    }

    public function test_it_throws_exception_when_validating_invalid_type(): void
    {
        $this->expectException(RecordValidationException::class);

        $data = ['$type' => 'app.bsky.feed.invalid'];

        $this->resolver->validateDiscriminated($data, ['app.bsky.feed.post']);
    }

    public function test_it_extracts_type_from_data(): void
    {
        $data = ['$type' => 'app.bsky.feed.post', 'text' => 'Hello'];

        $type = $this->resolver->extractType($data);

        $this->assertEquals('app.bsky.feed.post', $type);
    }

    public function test_it_returns_null_when_extracting_type_from_non_object(): void
    {
        $type = $this->resolver->extractType('not an object');

        $this->assertNull($type);
    }

    public function test_it_returns_null_when_extracting_type_without_type_field(): void
    {
        $data = ['text' => 'Hello'];

        $type = $this->resolver->extractType($data);

        $this->assertNull($type);
    }

    public function test_it_creates_discriminated_union_data(): void
    {
        $data = $this->resolver->createDiscriminated('app.bsky.feed.post', [
            'text' => 'Hello',
            'createdAt' => '2024-01-01T00:00:00Z',
        ]);

        $this->assertEquals([
            '$type' => 'app.bsky.feed.post',
            'text' => 'Hello',
            'createdAt' => '2024-01-01T00:00:00Z',
        ], $data);
    }

    public function test_it_checks_if_union_is_closed(): void
    {
        $closedUnion = ['closed' => true];
        $openUnion = ['closed' => false];
        $defaultUnion = [];

        $this->assertTrue($this->resolver->isClosed($closedUnion));
        $this->assertFalse($this->resolver->isClosed($openUnion));
        $this->assertFalse($this->resolver->isClosed($defaultUnion));
    }

    public function test_it_gets_union_types(): void
    {
        $unionDef = [
            'type' => 'union',
            'refs' => ['app.bsky.feed.post', 'app.bsky.feed.repost'],
        ];

        $types = $this->resolver->getTypes($unionDef);

        $this->assertEquals(['app.bsky.feed.post', 'app.bsky.feed.repost'], $types);
    }

    public function test_it_returns_empty_array_for_union_without_refs(): void
    {
        $unionDef = ['type' => 'union'];

        $types = $this->resolver->getTypes($unionDef);

        $this->assertEquals([], $types);
    }

    public function test_it_allows_setting_registry(): void
    {
        // Create a simple registry implementation
        $registry = new class () implements LexiconRegistry {
            public function register(LexiconDocument $document): void
            {
            }

            public function get(string $nsid): ?LexiconDocument
            {
                return null;
            }

            public function has(string $nsid): bool
            {
                return false;
            }

            public function all(): array
            {
                return [];
            }

            public function clear(): void
            {
            }
        };

        $result = $this->resolver->setRegistry($registry);

        $this->assertSame($this->resolver, $result);
    }

    public function test_it_handles_multiple_types_in_discriminated_union(): void
    {
        $refs = [
            'app.bsky.feed.post',
            'app.bsky.feed.repost',
            'app.bsky.feed.like',
        ];

        $unionDef = [
            'type' => 'union',
            'refs' => $refs,
            'closed' => true,
        ];

        foreach ($refs as $ref) {
            $data = ['$type' => $ref];
            $type = $this->resolver->resolve($data, $unionDef);
            $this->assertEquals($ref, $type);
        }
    }

    public function test_it_preserves_data_when_creating_discriminated_union(): void
    {
        $originalData = [
            'field1' => 'value1',
            'field2' => 123,
            'field3' => ['nested' => 'data'],
        ];

        $data = $this->resolver->createDiscriminated('app.bsky.feed.post', $originalData);

        $this->assertEquals('app.bsky.feed.post', $data['$type']);
        $this->assertEquals('value1', $data['field1']);
        $this->assertEquals(123, $data['field2']);
        $this->assertEquals(['nested' => 'data'], $data['field3']);
    }

    public function test_it_overwrites_existing_type_when_creating_discriminated_union(): void
    {
        $originalData = [
            '$type' => 'old.type',
            'text' => 'Hello',
        ];

        $data = $this->resolver->createDiscriminated('app.bsky.feed.post', $originalData);

        $this->assertEquals('app.bsky.feed.post', $data['$type']);
        $this->assertEquals('Hello', $data['text']);
    }
}
