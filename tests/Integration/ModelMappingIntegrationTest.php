<?php

namespace SocialDept\AtpSchema\Tests\Integration;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Contracts\Transformer;
use SocialDept\AtpSchema\Data\LexiconDocument;
use SocialDept\AtpSchema\Parser\SchemaLoader;
use SocialDept\AtpSchema\Services\ModelMapper;
use SocialDept\AtpSchema\Validation\Validator;

class ModelMappingIntegrationTest extends TestCase
{
    protected ModelMapper $mapper;

    protected Validator $validator;

    protected SchemaLoader $schemaLoader;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mapper = new ModelMapper();
        $this->schemaLoader = new SchemaLoader([]);
        $this->validator = new Validator($this->schemaLoader);
    }

    public function test_it_transforms_and_validates_complete_workflow(): void
    {
        // Register transformer
        $this->mapper->register('app.bsky.feed.post', new PostTransformer());

        // Load schema
        $schema = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'app.bsky.feed.post',
            'defs' => [
                'main' => [
                    'type' => 'record',
                    'key' => 'tid',
                    'record' => [
                        'type' => 'object',
                        'required' => ['text', 'createdAt'],
                        'properties' => [
                            'text' => ['type' => 'string', 'maxLength' => 300],
                            'createdAt' => ['type' => 'string', 'format' => 'datetime'],
                        ],
                    ],
                ],
            ],
        ]);

        // Transform to model
        $model = $this->mapper->fromArray('app.bsky.feed.post', [
            'text' => 'Hello, world!',
            'createdAt' => '2024-01-01T00:00:00Z',
        ]);

        // Verify model
        $this->assertInstanceOf(Post::class, $model);
        $this->assertEquals('Hello, world!', $model->text);

        // Transform back to array
        $data = $this->mapper->toArray('app.bsky.feed.post', $model);

        // Validate transformed data
        $result = $this->validator->validate($data, $schema);

        $this->assertTrue($result);
    }

    public function test_it_handles_multiple_model_transformations(): void
    {
        $this->mapper->registerMany([
            'app.bsky.feed.post' => new PostTransformer(),
            'app.bsky.feed.repost' => new RepostTransformer(),
        ]);

        $posts = [
            ['text' => 'First post', 'createdAt' => '2024-01-01T00:00:00Z'],
            ['text' => 'Second post', 'createdAt' => '2024-01-02T00:00:00Z'],
        ];

        $models = $this->mapper->fromArrayMany('app.bsky.feed.post', $posts);

        $this->assertCount(2, $models);
        $this->assertContainsOnlyInstancesOf(Post::class, $models);

        $arrays = $this->mapper->toArrayMany('app.bsky.feed.post', $models);

        $this->assertEquals($posts, $arrays);
    }

    public function test_it_extends_mapper_with_macros(): void
    {
        ModelMapper::macro('validateAndTransform', function ($type, $data, $schema) {
            // Validate first
            if (! $this->validator->validate($data, $schema)) {
                return null;
            }

            // Then transform
            return $this->fromArray($type, $data);
        });

        $this->mapper->validator = $this->validator;
        $this->mapper->register('app.bsky.feed.post', new PostTransformer());

        $schema = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'app.bsky.feed.post',
            'defs' => [
                'main' => [
                    'type' => 'record',
                    'key' => 'tid',
                    'record' => [
                        'type' => 'object',
                        'required' => ['text'],
                        'properties' => [
                            'text' => ['type' => 'string'],
                            'createdAt' => ['type' => 'string'],
                        ],
                    ],
                ],
            ],
        ]);

        $validData = ['text' => 'Hello', 'createdAt' => '2024-01-01T00:00:00Z'];

        $result = $this->mapper->validateAndTransform('app.bsky.feed.post', $validData, $schema);

        $this->assertInstanceOf(Post::class, $result);

        ModelMapper::flushMacros();
    }

    public function test_it_handles_nested_transformations(): void
    {
        $this->mapper->register('app.bsky.actor.profile', new ProfileTransformer());

        $data = [
            'displayName' => 'Alice',
            'description' => 'Developer',
            'avatar' => [
                'url' => 'https://example.com/avatar.jpg',
                'size' => 12345,
            ],
        ];

        $model = $this->mapper->fromArray('app.bsky.actor.profile', $data);

        $this->assertInstanceOf(Profile::class, $model);
        $this->assertEquals('Alice', $model->displayName);
        $this->assertIsArray($model->avatar);

        $transformed = $this->mapper->toArray('app.bsky.actor.profile', $model);

        $this->assertEquals($data, $transformed);
    }
}

// Test models and transformers
class Post
{
    public function __construct(
        public string $text,
        public string $createdAt
    ) {
    }
}

class PostTransformer implements Transformer
{
    public function fromArray(array $data): Post
    {
        return new Post(
            text: $data['text'],
            createdAt: $data['createdAt']
        );
    }

    public function toArray(mixed $model): array
    {
        return [
            'text' => $model->text,
            'createdAt' => $model->createdAt,
        ];
    }

    public function supports(string $type): bool
    {
        return $type === 'app.bsky.feed.post';
    }
}

class Repost
{
    public function __construct(
        public string $uri,
        public string $createdAt
    ) {
    }
}

class RepostTransformer implements Transformer
{
    public function fromArray(array $data): Repost
    {
        return new Repost(
            uri: $data['uri'],
            createdAt: $data['createdAt']
        );
    }

    public function toArray(mixed $model): array
    {
        return [
            'uri' => $model->uri,
            'createdAt' => $model->createdAt,
        ];
    }

    public function supports(string $type): bool
    {
        return $type === 'app.bsky.feed.repost';
    }
}

class Profile
{
    public function __construct(
        public string $displayName,
        public string $description,
        public array $avatar
    ) {
    }
}

class ProfileTransformer implements Transformer
{
    public function fromArray(array $data): Profile
    {
        return new Profile(
            displayName: $data['displayName'],
            description: $data['description'],
            avatar: $data['avatar']
        );
    }

    public function toArray(mixed $model): array
    {
        return [
            'displayName' => $model->displayName,
            'description' => $model->description,
            'avatar' => $model->avatar,
        ];
    }

    public function supports(string $type): bool
    {
        return $type === 'app.bsky.actor.profile';
    }
}
