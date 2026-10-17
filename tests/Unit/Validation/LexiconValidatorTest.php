<?php

namespace SocialDept\AtpSchema\Tests\Unit\Validation;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Data\LexiconDocument;
use SocialDept\AtpSchema\Exceptions\RecordValidationException;
use SocialDept\AtpSchema\Exceptions\SchemaValidationException;
use SocialDept\AtpSchema\Parser\SchemaLoader;
use SocialDept\AtpSchema\Validation\LexiconValidator;

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

    public function test_it_validates_procedure_input_against_its_declared_schema(): void
    {
        $document = $this->procedure([
            'encoding' => 'application/json',
            'schema' => [
                'type' => 'object',
                'required' => ['text'],
                'properties' => [
                    'text' => ['type' => 'string', 'maxLength' => 10],
                ],
            ],
        ]);

        $this->validator->validateProcedure($document, ['text' => 'hello']);

        $this->expectException(\SocialDept\AtpSchema\Exceptions\SchemaException::class);

        $this->validator->validateProcedure($document, ['text' => str_repeat('x', 50)]);
    }

    public function test_it_requires_procedure_input_fields_from_the_declared_schema(): void
    {
        $document = $this->procedure([
            'encoding' => 'application/json',
            'schema' => ['type' => 'object', 'required' => ['text'], 'properties' => ['text' => ['type' => 'string']]],
        ]);

        $this->expectException(\SocialDept\AtpSchema\Exceptions\SchemaException::class);
        $this->expectExceptionMessage("missing required property 'text'");

        $this->validator->validateProcedure($document, []);
    }

    public function test_it_skips_procedure_input_that_is_not_json(): void
    {
        $document = $this->procedure(['encoding' => '*/*']);

        $this->validator->validateProcedure($document, []);

        $this->assertTrue(true);
    }

    public function test_it_validates_query_parameters(): void
    {
        $document = LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'com.example.query',
            'defs' => [
                'main' => [
                    'type' => 'query',
                    'parameters' => [
                        'type' => 'params',
                        'required' => ['actor'],
                        'properties' => [
                            'actor' => ['type' => 'string'],
                            'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100],
                            'uris' => ['type' => 'array', 'items' => ['type' => 'string']],
                        ],
                    ],
                ],
            ],
        ]);

        $this->validator->validateQuery($document, ['actor' => 'alice', 'limit' => 50, 'uris' => ['at://a', 'at://b']]);

        foreach ([['limit' => 50], ['actor' => 'alice', 'limit' => 500]] as $params) {
            try {
                $this->validator->validateQuery($document, $params);
                $this->fail('Invalid parameters should be refused.');
            } catch (\SocialDept\AtpSchema\Exceptions\SchemaException) {
                $this->addToAssertionCount(1);
            }
        }
    }

    protected function procedure(array $input): LexiconDocument
    {
        return LexiconDocument::fromArray([
            'lexicon' => 1,
            'id' => 'com.example.procedure',
            'defs' => ['main' => ['type' => 'procedure', 'input' => $input]],
        ]);
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
