<?php

namespace SocialDept\Schema\Tests\Unit\Validation;

use Orchestra\Testbench\TestCase;
use SocialDept\Schema\Data\LexiconDocument;
use SocialDept\Schema\Exceptions\RecordValidationException;
use SocialDept\Schema\Exceptions\SchemaValidationException;
use SocialDept\Schema\Parser\SchemaLoader;
use SocialDept\Schema\Validation\LexiconValidator;

class LexiconValidatorTest extends TestCase
{
    protected LexiconValidator $validator;

    protected SchemaLoader $loader;

    protected function setUp(): void
    {
        parent::setUp();

        $fixturesPath = __DIR__.'/../../fixtures';
        $this->loader = new SchemaLoader([$fixturesPath], false);
        $this->validator = new LexiconValidator($this->loader);
    }

    public function test_it_validates_valid_record(): void
    {
        $record = [
            'text' => 'Hello, World!',
            'createdAt' => '2024-01-01T00:00:00Z',
        ];

        $this->validator->validateByNsid('app.bsky.feed.post', $record);

        $this->assertTrue(true);
    }

    public function test_it_throws_on_missing_required_field(): void
    {
        $record = [
            'text' => 'Hello, World!',
            // Missing createdAt
        ];

        $this->expectException(RecordValidationException::class);

        $this->validator->validateByNsid('app.bsky.feed.post', $record);
    }

    public function test_it_throws_on_invalid_field_type(): void
    {
        $record = [
            'text' => 123, // Should be string
            'createdAt' => '2024-01-01T00:00:00Z',
        ];

        $this->expectException(RecordValidationException::class);

        $this->validator->validateByNsid('app.bsky.feed.post', $record);
    }

    public function test_it_validates_record_with_lexicon_document(): void
    {
        $document = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'com.example.test',
            'defs' => [
                'main' => [
                    'type' => 'record',
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

        $this->validator->validateRecord($document, ['name' => 'John']);

        $this->assertTrue(true);
    }

    public function test_it_throws_on_non_record_schema(): void
    {
        $document = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'com.example.test',
            'defs' => [
                'main' => [
                    'type' => 'query',
                ],
            ],
        ]);

        $this->expectException(SchemaValidationException::class);
        $this->expectExceptionMessage('Schema is not a record type');

        $this->validator->validateRecord($document, ['name' => 'John']);
    }

    public function test_it_validates_procedure_input(): void
    {
        $document = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'com.example.test',
            'defs' => [
                'main' => [
                    'type' => 'procedure',
                    'input' => [
                        'type' => 'object',
                        'required' => ['name'],
                        'properties' => [
                            'name' => ['type' => 'string'],
                        ],
                    ],
                ],
            ],
        ]);

        $this->validator->validateProcedure($document, ['name' => 'John']);

        $this->assertTrue(true);
    }

    public function test_it_validates_with_contract_method(): void
    {
        $document = $this->loader->load('app.bsky.feed.post');

        $record = [
            'text' => 'Hello, World!',
            'createdAt' => '2024-01-01T00:00:00Z',
        ];

        $this->assertTrue($this->validator->validate($record, $document));
    }

    public function test_it_returns_false_for_invalid_record(): void
    {
        $document = $this->loader->load('app.bsky.feed.post');

        $record = [
            'text' => 'Hello, World!',
            // Missing createdAt
        ];

        $this->assertFalse($this->validator->validate($record, $document));
    }

    public function test_it_validates_with_errors(): void
    {
        $document = $this->loader->load('app.bsky.feed.post');

        $record = [
            'text' => 'Hello, World!',
            'createdAt' => '2024-01-01T00:00:00Z',
        ];

        $errors = $this->validator->validateWithErrors($record, $document);

        $this->assertEmpty($errors);
    }

    public function test_it_returns_errors_for_invalid_record(): void
    {
        $document = $this->loader->load('app.bsky.feed.post');

        $record = [
            'text' => 'Hello, World!',
            // Missing createdAt
        ];

        $errors = $this->validator->validateWithErrors($record, $document);

        $this->assertNotEmpty($errors);
        $this->assertIsArray($errors);
    }

    public function test_it_validates_specific_field(): void
    {
        $document = $this->loader->load('app.bsky.feed.post');

        $this->assertTrue($this->validator->validateField('Hello, World!', 'text', $document));
        $this->assertFalse($this->validator->validateField(123, 'text', $document));
    }

    public function test_it_sets_validation_mode(): void
    {
        $this->validator->setMode('lenient');

        $this->assertTrue(true);
    }
}
