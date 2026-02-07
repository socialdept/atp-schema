<?php

namespace SocialDept\AtpSchema\Tests\Unit\Validation;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Data\LexiconDocument;
use SocialDept\AtpSupport\Nsid;
use SocialDept\AtpSchema\Parser\SchemaLoader;
use SocialDept\AtpSchema\Validation\Validator;

class ValidatorTest extends TestCase
{
    protected Validator $validator;

    protected SchemaLoader $loader;

    protected function setUp(): void
    {
        parent::setUp();

        $fixturesPath = __DIR__.'/../../fixtures';
        $this->loader = new SchemaLoader([$fixturesPath], false);
        $this->validator = new Validator($this->loader);
    }

    public function test_it_validates_valid_data(): void
    {
        $document = $this->createDocument([
            'type' => 'record',
            'record' => [
                'type' => 'object',
                'required' => ['name'],
                'properties' => [
                    'name' => ['type' => 'string'],
                    'age' => ['type' => 'integer'],
                ],
            ],
        ]);

        $data = ['name' => 'John', 'age' => 30];

        $this->assertTrue($this->validator->validate($data, $document));
    }

    public function test_it_rejects_missing_required_field(): void
    {
        $document = $this->createDocument([
            'type' => 'record',
            'record' => [
                'type' => 'object',
                'required' => ['name', 'email'],
                'properties' => [
                    'name' => ['type' => 'string'],
                    'email' => ['type' => 'string'],
                ],
            ],
        ]);

        $data = ['name' => 'John'];

        $this->assertFalse($this->validator->validate($data, $document));

        $errors = $this->validator->validateWithErrors($data, $document);
        $this->assertArrayHasKey('email', $errors);
        $this->assertStringContainsString('Required', $errors['email'][0]);
    }

    public function test_it_validates_type_mismatch(): void
    {
        $document = $this->createDocument([
            'type' => 'record',
            'record' => [
                'type' => 'object',
                'required' => ['age'],
                'properties' => [
                    'age' => ['type' => 'integer'],
                ],
            ],
        ]);

        $data = ['age' => 'not a number'];

        $this->assertFalse($this->validator->validate($data, $document));

        $errors = $this->validator->validateWithErrors($data, $document);
        $this->assertArrayHasKey('age', $errors);
    }

    public function test_it_validates_string_max_length(): void
    {
        $document = $this->createDocument([
            'type' => 'record',
            'record' => [
                'type' => 'object',
                'required' => ['text'],
                'properties' => [
                    'text' => [
                        'type' => 'string',
                        'maxLength' => 10,
                    ],
                ],
            ],
        ]);

        $data = ['text' => 'This is way too long'];

        $this->assertFalse($this->validator->validate($data, $document));

        $errors = $this->validator->validateWithErrors($data, $document);
        $this->assertArrayHasKey('text', $errors);
        $this->assertStringContainsString('maximum length', $errors['text'][0]);
    }

    public function test_it_validates_string_min_length(): void
    {
        $document = $this->createDocument([
            'type' => 'record',
            'record' => [
                'type' => 'object',
                'required' => ['text'],
                'properties' => [
                    'text' => [
                        'type' => 'string',
                        'minLength' => 5,
                    ],
                ],
            ],
        ]);

        $data = ['text' => 'Hi'];

        $this->assertFalse($this->validator->validate($data, $document));

        $errors = $this->validator->validateWithErrors($data, $document);
        $this->assertArrayHasKey('text', $errors);
        $this->assertStringContainsString('minimum length', $errors['text'][0]);
    }

    public function test_it_validates_grapheme_constraints(): void
    {
        $document = $this->createDocument([
            'type' => 'record',
            'record' => [
                'type' => 'object',
                'required' => ['text'],
                'properties' => [
                    'text' => [
                        'type' => 'string',
                        'maxGraphemes' => 5,
                    ],
                ],
            ],
        ]);

        $data = ['text' => '😀😁😂😃😄😅']; // 6 graphemes

        $this->assertFalse($this->validator->validate($data, $document));
    }

    public function test_it_validates_number_maximum(): void
    {
        $document = $this->createDocument([
            'type' => 'record',
            'record' => [
                'type' => 'object',
                'required' => ['count'],
                'properties' => [
                    'count' => [
                        'type' => 'integer',
                        'maximum' => 100,
                    ],
                ],
            ],
        ]);

        $data = ['count' => 150];

        $this->assertFalse($this->validator->validate($data, $document));

        $errors = $this->validator->validateWithErrors($data, $document);
        $this->assertArrayHasKey('count', $errors);
        $this->assertStringContainsString('maximum', $errors['count'][0]);
    }

    public function test_it_validates_number_minimum(): void
    {
        $document = $this->createDocument([
            'type' => 'record',
            'record' => [
                'type' => 'object',
                'required' => ['count'],
                'properties' => [
                    'count' => [
                        'type' => 'integer',
                        'minimum' => 10,
                    ],
                ],
            ],
        ]);

        $data = ['count' => 5];

        $this->assertFalse($this->validator->validate($data, $document));

        $errors = $this->validator->validateWithErrors($data, $document);
        $this->assertArrayHasKey('count', $errors);
        $this->assertStringContainsString('minimum', $errors['count'][0]);
    }

    public function test_it_validates_array_max_items(): void
    {
        $document = $this->createDocument([
            'type' => 'record',
            'record' => [
                'type' => 'object',
                'required' => ['items'],
                'properties' => [
                    'items' => [
                        'type' => 'array',
                        'maxItems' => 3,
                        'items' => ['type' => 'string'],
                    ],
                ],
            ],
        ]);

        $data = ['items' => ['a', 'b', 'c', 'd']];

        $this->assertFalse($this->validator->validate($data, $document));

        $errors = $this->validator->validateWithErrors($data, $document);
        $this->assertArrayHasKey('items', $errors);
        $this->assertStringContainsString('maximum items', $errors['items'][0]);
    }

    public function test_it_validates_array_min_items(): void
    {
        $document = $this->createDocument([
            'type' => 'record',
            'record' => [
                'type' => 'object',
                'required' => ['items'],
                'properties' => [
                    'items' => [
                        'type' => 'array',
                        'minItems' => 2,
                        'items' => ['type' => 'string'],
                    ],
                ],
            ],
        ]);

        $data = ['items' => ['a']];

        $this->assertFalse($this->validator->validate($data, $document));

        $errors = $this->validator->validateWithErrors($data, $document);
        $this->assertArrayHasKey('items', $errors);
        $this->assertStringContainsString('minimum items', $errors['items'][0]);
    }

    public function test_it_validates_enum_constraint(): void
    {
        $document = $this->createDocument([
            'type' => 'record',
            'record' => [
                'type' => 'object',
                'required' => ['status'],
                'properties' => [
                    'status' => [
                        'type' => 'string',
                        'enum' => ['active', 'inactive', 'pending'],
                    ],
                ],
            ],
        ]);

        $data = ['status' => 'unknown'];

        $this->assertFalse($this->validator->validate($data, $document));

        $errors = $this->validator->validateWithErrors($data, $document);
        $this->assertArrayHasKey('status', $errors);
        $this->assertStringContainsString('one of:', $errors['status'][0]);
    }

    public function test_it_validates_const_constraint(): void
    {
        $document = $this->createDocument([
            'type' => 'record',
            'record' => [
                'type' => 'object',
                'required' => ['type'],
                'properties' => [
                    'type' => [
                        'type' => 'string',
                        'const' => 'post',
                    ],
                ],
            ],
        ]);

        $data = ['type' => 'comment'];

        $this->assertFalse($this->validator->validate($data, $document));

        $errors = $this->validator->validateWithErrors($data, $document);
        $this->assertArrayHasKey('type', $errors);
    }

    public function test_it_validates_nested_objects(): void
    {
        $document = $this->createDocument([
            'type' => 'record',
            'record' => [
                'type' => 'object',
                'required' => ['author'],
                'properties' => [
                    'author' => [
                        'type' => 'object',
                        'required' => ['name'],
                        'properties' => [
                            'name' => ['type' => 'string'],
                            'email' => ['type' => 'string'],
                        ],
                    ],
                ],
            ],
        ]);

        $data = ['author' => ['email' => 'john@example.com']];

        $this->assertFalse($this->validator->validate($data, $document));

        $errors = $this->validator->validateWithErrors($data, $document);
        $this->assertArrayHasKey('author.name', $errors);
    }

    public function test_it_validates_array_items(): void
    {
        $document = $this->createDocument([
            'type' => 'record',
            'record' => [
                'type' => 'object',
                'required' => ['tags'],
                'properties' => [
                    'tags' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'string',
                            'maxLength' => 10,
                        ],
                    ],
                ],
            ],
        ]);

        $data = ['tags' => ['short', 'this is way too long']];

        $this->assertFalse($this->validator->validate($data, $document));

        $errors = $this->validator->validateWithErrors($data, $document);
        $this->assertArrayHasKey('tags[1]', $errors);
    }

    public function test_strict_mode_rejects_unknown_fields(): void
    {
        $document = $this->createDocument([
            'type' => 'record',
            'record' => [
                'type' => 'object',
                'required' => ['name'],
                'properties' => [
                    'name' => ['type' => 'string'],
                ],
            ],
        ]);

        $data = ['name' => 'John', 'unknown' => 'value'];

        $this->validator->setMode(Validator::MODE_STRICT);
        $this->assertFalse($this->validator->validate($data, $document));

        $errors = $this->validator->validateWithErrors($data, $document);
        $this->assertArrayHasKey('unknown', $errors);
    }

    public function test_optimistic_mode_allows_unknown_fields(): void
    {
        $document = $this->createDocument([
            'type' => 'record',
            'record' => [
                'type' => 'object',
                'required' => ['name'],
                'properties' => [
                    'name' => ['type' => 'string'],
                ],
            ],
        ]);

        $data = ['name' => 'John', 'unknown' => 'value'];

        $this->validator->setMode(Validator::MODE_OPTIMISTIC);
        $this->assertTrue($this->validator->validate($data, $document));
    }

    public function test_lenient_mode_skips_required_validation(): void
    {
        $document = $this->createDocument([
            'type' => 'record',
            'record' => [
                'type' => 'object',
                'required' => ['name', 'email'],
                'properties' => [
                    'name' => ['type' => 'string'],
                    'email' => ['type' => 'string'],
                ],
            ],
        ]);

        $data = ['name' => 'John'];

        $this->validator->setMode(Validator::MODE_LENIENT);
        $this->assertTrue($this->validator->validate($data, $document));
    }

    public function test_lenient_mode_skips_constraint_validation(): void
    {
        $document = $this->createDocument([
            'type' => 'record',
            'record' => [
                'type' => 'object',
                'required' => ['text'],
                'properties' => [
                    'text' => [
                        'type' => 'string',
                        'maxLength' => 5,
                    ],
                ],
            ],
        ]);

        $data = ['text' => 'This is way too long'];

        $this->validator->setMode(Validator::MODE_LENIENT);
        $this->assertTrue($this->validator->validate($data, $document));
    }

    public function test_it_validates_specific_field(): void
    {
        $document = $this->createDocument([
            'type' => 'record',
            'record' => [
                'type' => 'object',
                'required' => ['name'],
                'properties' => [
                    'name' => [
                        'type' => 'string',
                        'maxLength' => 50,
                    ],
                    'age' => ['type' => 'integer'],
                ],
            ],
        ]);

        $this->assertTrue($this->validator->validateField('John', 'name', $document));
        $this->assertFalse($this->validator->validateField('not a number', 'age', $document));
    }

    public function test_it_validates_field_constraints(): void
    {
        $document = $this->createDocument([
            'type' => 'record',
            'record' => [
                'type' => 'object',
                'required' => ['name'],
                'properties' => [
                    'name' => [
                        'type' => 'string',
                        'maxLength' => 5,
                    ],
                ],
            ],
        ]);

        $this->assertFalse($this->validator->validateField('John Doe', 'name', $document));
    }

    public function test_it_rejects_invalid_validation_mode(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->validator->setMode('invalid');
    }

    public function test_it_returns_current_mode(): void
    {
        $this->assertEquals(Validator::MODE_STRICT, $this->validator->getMode());

        $this->validator->setMode(Validator::MODE_LENIENT);
        $this->assertEquals(Validator::MODE_LENIENT, $this->validator->getMode());
    }

    public function test_it_returns_empty_errors_for_valid_data(): void
    {
        $document = $this->createDocument([
            'type' => 'record',
            'record' => [
                'type' => 'object',
                'required' => ['name'],
                'properties' => [
                    'name' => ['type' => 'string'],
                ],
            ],
        ]);

        $data = ['name' => 'John'];

        $errors = $this->validator->validateWithErrors($data, $document);
        $this->assertEmpty($errors);
    }

    public function test_it_validates_object_type_definition(): void
    {
        $document = $this->createDocument([
            'type' => 'object',
            'required' => ['name'],
            'properties' => [
                'name' => ['type' => 'string'],
            ],
        ]);

        $data = ['name' => 'John'];

        $this->assertTrue($this->validator->validate($data, $document));
    }

    public function test_it_validates_multiple_errors(): void
    {
        $document = $this->createDocument([
            'type' => 'record',
            'record' => [
                'type' => 'object',
                'required' => ['name', 'age', 'email'],
                'properties' => [
                    'name' => ['type' => 'string'],
                    'age' => ['type' => 'integer'],
                    'email' => ['type' => 'string'],
                ],
            ],
        ]);

        $data = ['name' => 'John'];

        $errors = $this->validator->validateWithErrors($data, $document);
        $this->assertCount(2, $errors); // Missing age and email
        $this->assertArrayHasKey('age', $errors);
        $this->assertArrayHasKey('email', $errors);
    }

    /**
     * Helper to create a test document.
     *
     * @param  array<string, mixed>  $mainDef
     */
    protected function createDocument(array $mainDef): LexiconDocument
    {
        return new LexiconDocument(
            lexicon: 1,
            id: Nsid::parse('com.example.test'),
            defs: ['main' => $mainDef],
            description: null,
            source: null,
            raw: []
        );
    }
}
