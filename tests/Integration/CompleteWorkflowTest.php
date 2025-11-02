<?php

namespace SocialDept\Schema\Tests\Integration;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase;
use SocialDept\Schema\Contracts\LexiconRegistry;
use SocialDept\Schema\Data\LexiconDocument;
use SocialDept\Schema\Parser\SchemaLoader;
use SocialDept\Schema\Services\BlobHandler;
use SocialDept\Schema\Services\UnionResolver;
use SocialDept\Schema\Support\ExtensionManager;
use SocialDept\Schema\Validation\Validator;

class CompleteWorkflowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    public function test_complete_post_creation_workflow(): void
    {
        // Step 1: Load schema
        $schemaLoader = new SchemaLoader([]);
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
                            'text' => [
                                'type' => 'string',
                                'maxLength' => 300,
                                'maxGraphemes' => 300,
                            ],
                            'createdAt' => [
                                'type' => 'string',
                                'format' => 'datetime',
                            ],
                            'embed' => [
                                'type' => 'union',
                                'refs' => ['app.bsky.embed.images', 'app.bsky.embed.external'],
                                'closed' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        // Step 2: Create post data
        $postData = [
            'text' => 'Check out this amazing photo!',
            'createdAt' => '2024-01-01T12:00:00Z',
            'embed' => [
                '$type' => 'app.bsky.embed.images',
                'images' => [],
            ],
        ];

        // Step 3: Validate
        $validator = new Validator($schemaLoader);
        $isValid = $validator->validate($postData, $schema);

        $this->assertTrue($isValid);

        // Step 4: Verify union type
        $unionResolver = new UnionResolver();
        $embedType = $unionResolver->extractType($postData['embed']);

        $this->assertEquals('app.bsky.embed.images', $embedType);
    }

    public function test_image_upload_with_validation_workflow(): void
    {
        // Step 1: Upload image
        $blobHandler = new BlobHandler('local');
        $file = UploadedFile::fake()->image('photo.jpg', 800, 600);

        $constraints = [
            'accept' => ['image/*'],
            'maxSize' => 1024 * 1024 * 5, // 5MB
        ];

        $blob = $blobHandler->store($file, $constraints);

        // Step 2: Verify blob
        $this->assertStringStartsWith('bafyrei', $blob->ref);
        $this->assertTrue($blob->isImage());

        // Step 3: Create record with blob
        $schemaLoader = new SchemaLoader([]);
        $schema = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'app.bsky.embed.images',
            'defs' => [
                'main' => [
                    'type' => 'record',
                    'key' => 'tid',
                    'record' => [
                        'type' => 'object',
                        'properties' => [
                            'images' => [
                                'type' => 'array',
                                'maxLength' => 4,
                                'items' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'image' => [
                                            'type' => 'blob',
                                            'accept' => ['image/*'],
                                        ],
                                        'alt' => [
                                            'type' => 'string',
                                            'maxLength' => 1000,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $data = [
            'images' => [
                [
                    'image' => $blob->toArray(),
                    'alt' => 'A beautiful sunset',
                ],
            ],
        ];

        // Step 4: Validate
        $validator = new Validator($schemaLoader);
        $isValid = $validator->validate($data, $schema);

        $this->assertTrue($isValid);

        // Step 5: Retrieve blob content
        $content = $blobHandler->get($blob->ref);

        $this->assertNotNull($content);
    }

    public function test_validation_error_formatting_workflow(): void
    {
        $schemaLoader = new SchemaLoader([]);
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
                            'text' => ['type' => 'string', 'maxLength' => 10],
                            'createdAt' => ['type' => 'string', 'format' => 'datetime'],
                        ],
                    ],
                ],
            ],
        ]);

        $invalidData = [
            'text' => 'This text is way too long for the constraint',
        ];

        $validator = new Validator($schemaLoader);
        $validator->validate($invalidData, $schema);

        // Get errors
        $errors = $validator->validateWithErrors($invalidData, $schema);

        $this->assertNotEmpty($errors);

        // Errors are in Laravel format
        $this->assertArrayHasKey('text', $errors);
        $this->assertArrayHasKey('createdAt', $errors); // Missing required field

        // Verify error messages
        $this->assertIsArray($errors['text']);
        $this->assertNotEmpty($errors['text'][0]);
        $this->assertIsArray($errors['createdAt']);
        $this->assertNotEmpty($errors['createdAt'][0]);
    }

    public function test_extension_hooks_workflow(): void
    {
        $extensions = new ExtensionManager();

        // Register validation hook
        $extensions->hook('before:validate', function ($data) {
            // Transform data before validation
            if (isset($data['text'])) {
                $data['text'] = trim($data['text']);
            }

            return $data;
        });

        // Register post-validation hook
        $executed = false;
        $extensions->hook('after:validate', function ($result) use (&$executed) {
            $executed = true;

            return $result;
        });

        // Execute hooks
        $data = ['text' => '  Hello, world!  '];
        $transformed = $extensions->filter('before:validate', $data);

        $this->assertEquals('Hello, world!', $transformed['text']);

        $extensions->execute('after:validate', true);

        $this->assertTrue($executed);
    }

    public function test_schema_registry_workflow(): void
    {
        $registry = new SimpleRegistry();

        // Register schemas
        $schemaLoader = new SchemaLoader([]);

        $postSchema = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'app.bsky.feed.post',
            'defs' => ['main' => ['type' => 'record', 'key' => 'tid']],
        ]);

        $repostSchema = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'app.bsky.feed.repost',
            'defs' => ['main' => ['type' => 'record', 'key' => 'tid']],
        ]);

        $registry->register($postSchema);
        $registry->register($repostSchema);

        // Retrieve schemas
        $this->assertTrue($registry->has('app.bsky.feed.post'));
        $this->assertTrue($registry->has('app.bsky.feed.repost'));

        $retrieved = $registry->get('app.bsky.feed.post');
        $this->assertInstanceOf(LexiconDocument::class, $retrieved);
        $this->assertEquals('app.bsky.feed.post', $retrieved->getNsid());

        // Use with union resolver
        $unionResolver = new UnionResolver($registry);

        $data = ['$type' => 'app.bsky.feed.post'];
        $unionDef = [
            'type' => 'union',
            'refs' => ['app.bsky.feed.post', 'app.bsky.feed.repost'],
            'closed' => true,
        ];

        $typeDef = $unionResolver->getTypeDefinition($data, $unionDef);

        $this->assertInstanceOf(LexiconDocument::class, $typeDef);
        $this->assertEquals('app.bsky.feed.post', $typeDef->getNsid());
    }

    public function test_multimode_validation_workflow(): void
    {
        $schemaLoader = new SchemaLoader([]);
        $schema = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'app.test.record',
            'defs' => [
                'main' => [
                    'type' => 'record',
                    'key' => 'tid',
                    'record' => [
                        'type' => 'object',
                        'required' => ['name'],
                        'properties' => [
                            'name' => ['type' => 'string'],
                            'age' => ['type' => 'integer', 'minimum' => 0],
                        ],
                    ],
                ],
            ],
        ]);

        $validator = new Validator($schemaLoader);

        $dataWithExtra = [
            'name' => 'Alice',
            'age' => 30,
            'unknownField' => 'value',
        ];

        // Strict mode
        $validator->setMode(Validator::MODE_STRICT);
        $this->assertFalse($validator->validate($dataWithExtra, $schema));

        // Optimistic mode
        $validator->setMode(Validator::MODE_OPTIMISTIC);
        $this->assertTrue($validator->validate($dataWithExtra, $schema));

        // Lenient mode
        $validator->setMode(Validator::MODE_LENIENT);
        $this->assertTrue($validator->validate($dataWithExtra, $schema));

        // Lenient mode ignores constraints
        $invalidAge = ['name' => 'Bob', 'age' => -5];
        $validator->setMode(Validator::MODE_LENIENT);
        $this->assertTrue($validator->validate($invalidAge, $schema));

        // But optimistic/strict catch it
        $validator->setMode(Validator::MODE_OPTIMISTIC);
        $this->assertFalse($validator->validate($invalidAge, $schema));
    }
}

// Simple registry implementation for testing
class SimpleRegistry implements LexiconRegistry
{
    protected array $schemas = [];

    public function register(LexiconDocument $document): void
    {
        $this->schemas[$document->getNsid()] = $document;
    }

    public function get(string $nsid): ?LexiconDocument
    {
        return $this->schemas[$nsid] ?? null;
    }

    public function has(string $nsid): bool
    {
        return isset($this->schemas[$nsid]);
    }

    public function all(): array
    {
        return $this->schemas;
    }

    public function clear(): void
    {
        $this->schemas = [];
    }
}
