<?php

namespace SocialDept\AtpSchema\Tests\Unit\Services;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Contracts\Transformer;
use SocialDept\AtpSchema\Exceptions\SchemaException;
use SocialDept\AtpSchema\Services\ModelMapper;

class ModelMapperTest extends TestCase
{
    protected ModelMapper $mapper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mapper = new ModelMapper();
    }

    public function test_it_registers_transformer(): void
    {
        $transformer = $this->createTestTransformer('app.bsky.feed.post');

        $this->mapper->register('app.bsky.feed.post', $transformer);

        $this->assertTrue($this->mapper->has('app.bsky.feed.post'));
    }

    public function test_it_registers_multiple_transformers(): void
    {
        $transformer1 = $this->createTestTransformer('app.bsky.feed.post');
        $transformer2 = $this->createTestTransformer('app.bsky.feed.repost');

        $this->mapper->registerMany([
            'app.bsky.feed.post' => $transformer1,
            'app.bsky.feed.repost' => $transformer2,
        ]);

        $this->assertTrue($this->mapper->has('app.bsky.feed.post'));
        $this->assertTrue($this->mapper->has('app.bsky.feed.repost'));
    }

    public function test_it_transforms_from_array(): void
    {
        $transformer = $this->createTestTransformer('app.bsky.feed.post');
        $this->mapper->register('app.bsky.feed.post', $transformer);

        $data = ['text' => 'Hello, world!'];
        $model = $this->mapper->fromArray('app.bsky.feed.post', $data);

        $this->assertInstanceOf(\stdClass::class, $model);
        $this->assertEquals('Hello, world!', $model->text);
    }

    public function test_it_transforms_to_array(): void
    {
        $transformer = $this->createTestTransformer('app.bsky.feed.post');
        $this->mapper->register('app.bsky.feed.post', $transformer);

        $model = (object) ['text' => 'Hello, world!'];
        $data = $this->mapper->toArray('app.bsky.feed.post', $model);

        $this->assertEquals(['text' => 'Hello, world!'], $data);
    }

    public function test_it_throws_exception_when_transformer_not_found_for_from_array(): void
    {
        $this->expectException(SchemaException::class);

        $this->mapper->fromArray('unknown.type', []);
    }

    public function test_it_throws_exception_when_transformer_not_found_for_to_array(): void
    {
        $this->expectException(SchemaException::class);

        $this->mapper->toArray('unknown.type', new \stdClass());
    }

    public function test_it_transforms_multiple_items_from_arrays(): void
    {
        $transformer = $this->createTestTransformer('app.bsky.feed.post');
        $this->mapper->register('app.bsky.feed.post', $transformer);

        $items = [
            ['text' => 'First post'],
            ['text' => 'Second post'],
        ];

        $models = $this->mapper->fromArrayMany('app.bsky.feed.post', $items);

        $this->assertCount(2, $models);
        $this->assertEquals('First post', $models[0]->text);
        $this->assertEquals('Second post', $models[1]->text);
    }

    public function test_it_transforms_multiple_items_to_arrays(): void
    {
        $transformer = $this->createTestTransformer('app.bsky.feed.post');
        $this->mapper->register('app.bsky.feed.post', $transformer);

        $models = [
            (object) ['text' => 'First post'],
            (object) ['text' => 'Second post'],
        ];

        $arrays = $this->mapper->toArrayMany('app.bsky.feed.post', $models);

        $this->assertCount(2, $arrays);
        $this->assertEquals(['text' => 'First post'], $arrays[0]);
        $this->assertEquals(['text' => 'Second post'], $arrays[1]);
    }

    public function test_it_gets_transformer(): void
    {
        $transformer = $this->createTestTransformer('app.bsky.feed.post');
        $this->mapper->register('app.bsky.feed.post', $transformer);

        $retrieved = $this->mapper->getTransformer('app.bsky.feed.post');

        $this->assertSame($transformer, $retrieved);
    }

    public function test_it_returns_null_for_missing_transformer(): void
    {
        $transformer = $this->mapper->getTransformer('unknown.type');

        $this->assertNull($transformer);
    }

    public function test_it_checks_if_has_transformer(): void
    {
        $transformer = $this->createTestTransformer('app.bsky.feed.post');
        $this->mapper->register('app.bsky.feed.post', $transformer);

        $this->assertTrue($this->mapper->has('app.bsky.feed.post'));
        $this->assertFalse($this->mapper->has('unknown.type'));
    }

    public function test_it_unregisters_transformer(): void
    {
        $transformer = $this->createTestTransformer('app.bsky.feed.post');
        $this->mapper->register('app.bsky.feed.post', $transformer);

        $this->assertTrue($this->mapper->has('app.bsky.feed.post'));

        $this->mapper->unregister('app.bsky.feed.post');

        $this->assertFalse($this->mapper->has('app.bsky.feed.post'));
    }

    public function test_it_gets_all_transformers(): void
    {
        $transformer1 = $this->createTestTransformer('app.bsky.feed.post');
        $transformer2 = $this->createTestTransformer('app.bsky.feed.repost');

        $this->mapper->registerMany([
            'app.bsky.feed.post' => $transformer1,
            'app.bsky.feed.repost' => $transformer2,
        ]);

        $all = $this->mapper->all();

        $this->assertCount(2, $all);
        $this->assertArrayHasKey('app.bsky.feed.post', $all);
        $this->assertArrayHasKey('app.bsky.feed.repost', $all);
    }

    public function test_it_clears_all_transformers(): void
    {
        $transformer = $this->createTestTransformer('app.bsky.feed.post');
        $this->mapper->register('app.bsky.feed.post', $transformer);

        $this->assertEquals(1, $this->mapper->count());

        $this->mapper->clear();

        $this->assertEquals(0, $this->mapper->count());
    }

    public function test_it_tries_from_array_with_missing_transformer(): void
    {
        $result = $this->mapper->tryFromArray('unknown.type', []);

        $this->assertNull($result);
    }

    public function test_it_tries_from_array_with_existing_transformer(): void
    {
        $transformer = $this->createTestTransformer('app.bsky.feed.post');
        $this->mapper->register('app.bsky.feed.post', $transformer);

        $result = $this->mapper->tryFromArray('app.bsky.feed.post', ['text' => 'Hello']);

        $this->assertNotNull($result);
        $this->assertEquals('Hello', $result->text);
    }

    public function test_it_tries_to_array_with_missing_transformer(): void
    {
        $result = $this->mapper->tryToArray('unknown.type', new \stdClass());

        $this->assertNull($result);
    }

    public function test_it_tries_to_array_with_existing_transformer(): void
    {
        $transformer = $this->createTestTransformer('app.bsky.feed.post');
        $this->mapper->register('app.bsky.feed.post', $transformer);

        $model = (object) ['text' => 'Hello'];
        $result = $this->mapper->tryToArray('app.bsky.feed.post', $model);

        $this->assertNotNull($result);
        $this->assertEquals(['text' => 'Hello'], $result);
    }

    public function test_it_counts_transformers(): void
    {
        $this->assertEquals(0, $this->mapper->count());

        $this->mapper->register('app.bsky.feed.post', $this->createTestTransformer('app.bsky.feed.post'));

        $this->assertEquals(1, $this->mapper->count());

        $this->mapper->register('app.bsky.feed.repost', $this->createTestTransformer('app.bsky.feed.repost'));

        $this->assertEquals(2, $this->mapper->count());
    }

    public function test_it_uses_wildcard_transformer(): void
    {
        $transformer = $this->createWildcardTransformer('app.bsky.feed.*');
        $this->mapper->register('app.bsky.feed.*', $transformer);

        $this->assertTrue($this->mapper->has('app.bsky.feed.post'));
        $this->assertTrue($this->mapper->has('app.bsky.feed.repost'));
        $this->assertFalse($this->mapper->has('app.bsky.graph.follow'));
    }

    public function test_it_prefers_exact_match_over_wildcard(): void
    {
        $wildcardTransformer = $this->createWildcardTransformer('app.bsky.feed.*');
        $exactTransformer = $this->createTestTransformer('app.bsky.feed.post');

        $this->mapper->register('app.bsky.feed.*', $wildcardTransformer);
        $this->mapper->register('app.bsky.feed.post', $exactTransformer);

        $retrieved = $this->mapper->getTransformer('app.bsky.feed.post');

        $this->assertSame($exactTransformer, $retrieved);
    }

    public function test_it_chains_register_calls(): void
    {
        $transformer1 = $this->createTestTransformer('type1');
        $transformer2 = $this->createTestTransformer('type2');

        $result = $this->mapper
            ->register('type1', $transformer1)
            ->register('type2', $transformer2);

        $this->assertSame($this->mapper, $result);
        $this->assertTrue($this->mapper->has('type1'));
        $this->assertTrue($this->mapper->has('type2'));
    }

    public function test_it_chains_register_many_calls(): void
    {
        $result = $this->mapper->registerMany([
            'type1' => $this->createTestTransformer('type1'),
            'type2' => $this->createTestTransformer('type2'),
        ]);

        $this->assertSame($this->mapper, $result);
    }

    public function test_it_chains_unregister_calls(): void
    {
        $this->mapper->register('type1', $this->createTestTransformer('type1'));

        $result = $this->mapper->unregister('type1');

        $this->assertSame($this->mapper, $result);
    }

    public function test_it_chains_clear_calls(): void
    {
        $result = $this->mapper->clear();

        $this->assertSame($this->mapper, $result);
    }

    protected function createTestTransformer(string $type): Transformer
    {
        return new class ($type) implements Transformer {
            public function __construct(protected string $type)
            {
            }

            public function fromArray(array $data): mixed
            {
                return (object) $data;
            }

            public function toArray(mixed $model): array
            {
                return (array) $model;
            }

            public function supports(string $type): bool
            {
                return $type === $this->type;
            }
        };
    }

    protected function createWildcardTransformer(string $pattern): Transformer
    {
        return new class ($pattern) implements Transformer {
            public function __construct(protected string $pattern)
            {
            }

            public function fromArray(array $data): mixed
            {
                return (object) $data;
            }

            public function toArray(mixed $model): array
            {
                return (array) $model;
            }

            public function supports(string $type): bool
            {
                $regex = '/^'.str_replace('\\*', '.*', preg_quote($this->pattern, '/')).'$/';

                return (bool) preg_match($regex, $type);
            }
        };
    }
}
