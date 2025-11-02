<?php

namespace SocialDept\Schema\Tests\Integration;

use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase;
use SocialDept\Schema\Data\LexiconDocument;
use SocialDept\Schema\Parser\SchemaLoader;
use SocialDept\Schema\Services\BlobHandler;
use SocialDept\Schema\Validation\Validator;

class ValidationIntegrationTest extends TestCase
{
    protected SchemaLoader $schemaLoader;

    protected Validator $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->schemaLoader = new SchemaLoader([]);
        $this->validator = new Validator($this->schemaLoader);
    }

    public function test_it_validates_complete_record_with_all_types(): void
    {
        $schema = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'app.test.post',
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
                            'facets' => [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'index' => ['type' => 'integer'],
                                        'features' => [
                                            'type' => 'array',
                                            'items' => ['type' => 'string'],
                                        ],
                                    ],
                                ],
                            ],
                            'embed' => [
                                'type' => 'union',
                                'refs' => ['app.test.images', 'app.test.external'],
                                'closed' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $validData = [
            'text' => 'Hello, world!',
            'createdAt' => '2024-01-01T00:00:00Z',
            'facets' => [
                [
                    'index' => 0,
                    'features' => ['mention', 'link'],
                ],
            ],
            'embed' => [
                '$type' => 'app.test.images',
                'images' => [],
            ],
        ];

        $result = $this->validator->validate($validData, $schema);

        $this->assertTrue($result);
    }

    public function test_it_detects_multiple_validation_errors(): void
    {
        $schema = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'app.test.post',
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
                                'maxLength' => 10,
                            ],
                            'createdAt' => [
                                'type' => 'string',
                                'format' => 'datetime',
                            ],
                            'count' => [
                                'type' => 'integer',
                                'minimum' => 0,
                                'maximum' => 100,
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $invalidData = [
            'text' => 'This is a very long text that exceeds the maximum length',
            'count' => 150,
        ];

        $result = $this->validator->validate($invalidData, $schema);

        $this->assertFalse($result);

        $errors = $this->validator->validateWithErrors($invalidData, $schema);

        $this->assertArrayHasKey('text', $errors);
        $this->assertArrayHasKey('createdAt', $errors); // Missing required field
        $this->assertArrayHasKey('count', $errors);
    }

    public function test_it_validates_nested_objects_deeply(): void
    {
        $schema = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'app.test.nested',
            'defs' => [
                'main' => [
                    'type' => 'record',
                    'key' => 'tid',
                    'record' => [
                        'type' => 'object',
                        'properties' => [
                            'user' => [
                                'type' => 'object',
                                'required' => ['name'],
                                'properties' => [
                                    'name' => ['type' => 'string'],
                                    'profile' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'bio' => ['type' => 'string', 'maxLength' => 100],
                                            'avatar' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'url' => ['type' => 'string', 'format' => 'uri'],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $validData = [
            'user' => [
                'name' => 'Alice',
                'profile' => [
                    'bio' => 'Software developer',
                    'avatar' => [
                        'url' => 'https://example.com/avatar.jpg',
                    ],
                ],
            ],
        ];

        $result = $this->validator->validate($validData, $schema);

        $this->assertTrue($result);
    }

    public function test_it_validates_with_blob_handler_integration(): void
    {
        Storage::fake('local');

        $blobHandler = new BlobHandler('local');

        $schema = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'app.test.image',
            'defs' => [
                'main' => [
                    'type' => 'record',
                    'key' => 'tid',
                    'record' => [
                        'type' => 'object',
                        'required' => ['image'],
                        'properties' => [
                            'image' => [
                                'type' => 'blob',
                                'accept' => ['image/*'],
                                'maxSize' => 1024 * 1024,
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        // Create a blob
        $blob = $blobHandler->storeFromString('test image content', 'image/png');

        // Validate with blob data
        $validData = [
            'image' => $blob->toArray(),
        ];

        $result = $this->validator->validate($validData, $schema);

        $this->assertTrue($result);
    }

    public function test_it_handles_array_validation_with_constraints(): void
    {
        $schema = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'app.test.list',
            'defs' => [
                'main' => [
                    'type' => 'record',
                    'key' => 'tid',
                    'record' => [
                        'type' => 'object',
                        'properties' => [
                            'tags' => [
                                'type' => 'array',
                                'minLength' => 1,
                                'maxLength' => 5,
                                'items' => [
                                    'type' => 'string',
                                    'maxLength' => 20,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $validData = [
            'tags' => ['tag1', 'tag2', 'tag3'],
        ];

        $result = $this->validator->validate($validData, $schema);

        $this->assertTrue($result);

        // Invalid: tag item too long
        $invalidData = [
            'tags' => ['tag1', 'this is a very long tag that exceeds the maximum length of 20 characters'],
        ];

        $result = $this->validator->validate($invalidData, $schema);

        $this->assertFalse($result);
    }

    public function test_it_validates_different_modes(): void
    {
        $schema = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'app.test.strict',
            'defs' => [
                'main' => [
                    'type' => 'record',
                    'key' => 'tid',
                    'record' => [
                        'type' => 'object',
                        'required' => ['name'],
                        'properties' => [
                            'name' => ['type' => 'string'],
                        ],
                    ],
                ],
            ],
        ]);

        $dataWithUnknownField = [
            'name' => 'Alice',
            'unknownField' => 'value',
        ];

        // STRICT mode - should reject unknown fields
        $this->validator->setMode(Validator::MODE_STRICT);
        $result = $this->validator->validate($dataWithUnknownField, $schema);
        $this->assertFalse($result);

        // OPTIMISTIC mode - should allow unknown fields
        $this->validator->setMode(Validator::MODE_OPTIMISTIC);
        $result = $this->validator->validate($dataWithUnknownField, $schema);
        $this->assertTrue($result);
    }

    public function test_it_validates_with_all_format_types(): void
    {
        $schema = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'app.test.formats',
            'defs' => [
                'main' => [
                    'type' => 'record',
                    'key' => 'tid',
                    'record' => [
                        'type' => 'object',
                        'properties' => [
                            'datetime' => ['type' => 'string', 'format' => 'datetime'],
                            'uri' => ['type' => 'string', 'format' => 'uri'],
                            'atUri' => ['type' => 'string', 'format' => 'at-uri'],
                            'did' => ['type' => 'string', 'format' => 'did'],
                            'nsid' => ['type' => 'string', 'format' => 'nsid'],
                            'cid' => ['type' => 'string', 'format' => 'cid'],
                        ],
                    ],
                ],
            ],
        ]);

        $validData = [
            'datetime' => '2024-01-01T00:00:00Z',
            'uri' => 'https://example.com',
            'atUri' => 'at://did:plc:abc123/app.bsky.feed.post/123',
            'did' => 'did:plc:abc123',
            'nsid' => 'app.bsky.feed.post',
            'cid' => 'bafyreigabcdefghijklmnopqrstuvwxyz234567',
        ];

        $result = $this->validator->validate($validData, $schema);

        $this->assertTrue($result);
    }
}
