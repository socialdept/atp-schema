<?php

namespace SocialDept\Schema\Tests\Unit\Generator;

use Orchestra\Testbench\TestCase;
use SocialDept\Schema\Generator\PropertyGenerator;

class PropertyGeneratorTest extends TestCase
{
    protected PropertyGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = new PropertyGenerator;
    }

    public function test_it_generates_required_string_property(): void
    {
        $property = $this->generator->generate('name', ['type' => 'string'], ['name']);

        $this->assertStringContainsString('public readonly string $name;', $property);
        $this->assertStringContainsString('@var string', $property);
    }

    public function test_it_generates_optional_string_property(): void
    {
        $property = $this->generator->generate('nickname', ['type' => 'string'], []);

        $this->assertStringContainsString('public readonly ?string $nickname;', $property);
        $this->assertStringContainsString('@var string|null', $property);
    }

    public function test_it_generates_integer_property(): void
    {
        $property = $this->generator->generate('age', ['type' => 'integer'], ['age']);

        $this->assertStringContainsString('public readonly int $age;', $property);
        $this->assertStringContainsString('@var int', $property);
    }

    public function test_it_generates_boolean_property(): void
    {
        $property = $this->generator->generate('active', ['type' => 'boolean'], ['active']);

        $this->assertStringContainsString('public readonly bool $active;', $property);
        $this->assertStringContainsString('@var bool', $property);
    }

    public function test_it_generates_array_property(): void
    {
        $property = $this->generator->generate('tags', ['type' => 'array'], ['tags']);

        $this->assertStringContainsString('public readonly array $tags;', $property);
        $this->assertStringContainsString('@var array', $property);
    }

    public function test_it_includes_description_in_docblock(): void
    {
        $property = $this->generator->generate(
            'name',
            [
                'type' => 'string',
                'description' => 'The user name',
            ],
            ['name']
        );

        $this->assertStringContainsString('* The user name', $property);
    }

    public function test_it_generates_property_with_default_value(): void
    {
        $property = $this->generator->generate(
            'status',
            [
                'type' => 'string',
                'default' => 'active',
            ],
            []
        );

        $this->assertStringContainsString("= 'active'", $property);
    }

    public function test_it_generates_multiple_properties(): void
    {
        $properties = $this->generator->generateMultiple(
            [
                'name' => ['type' => 'string'],
                'age' => ['type' => 'integer'],
            ],
            ['name', 'age']
        );

        $this->assertCount(2, $properties);
        $this->assertStringContainsString('$name', $properties[0]);
        $this->assertStringContainsString('$age', $properties[1]);
    }

    public function test_it_generates_property_signature(): void
    {
        $signature = $this->generator->generateSignature('name', ['type' => 'string'], ['name']);

        $this->assertSame('string $name', $signature);
    }

    public function test_it_generates_optional_property_signature(): void
    {
        $signature = $this->generator->generateSignature('nickname', ['type' => 'string'], []);

        $this->assertSame('?string $nickname', $signature);
    }

    public function test_it_generates_promoted_property(): void
    {
        $promoted = $this->generator->generatePromoted('name', ['type' => 'string'], ['name']);

        $this->assertSame('public readonly string $name', $promoted);
    }

    public function test_it_generates_optional_promoted_property(): void
    {
        $promoted = $this->generator->generatePromoted('nickname', ['type' => 'string'], []);

        $this->assertSame('public readonly ?string $nickname', $promoted);
    }

    public function test_it_checks_if_property_is_nullable(): void
    {
        $this->assertFalse($this->generator->isNullable('name', ['type' => 'string'], ['name']));
        $this->assertTrue($this->generator->isNullable('nickname', ['type' => 'string'], []));
    }

    public function test_it_gets_property_type(): void
    {
        $type = $this->generator->getType(['type' => 'string']);

        $this->assertSame('string', $type);
    }

    public function test_it_gets_nullable_property_type(): void
    {
        $type = $this->generator->getType(['type' => 'string'], true);

        $this->assertSame('?string', $type);
    }

    public function test_it_gets_property_doc_type(): void
    {
        $docType = $this->generator->getDocType(['type' => 'string']);

        $this->assertSame('string', $docType);
    }

    public function test_it_gets_nullable_property_doc_type(): void
    {
        $docType = $this->generator->getDocType(['type' => 'string'], true);

        $this->assertSame('string|null', $docType);
    }

    public function test_it_handles_blob_type(): void
    {
        $property = $this->generator->generate('image', ['type' => 'blob'], []);

        $this->assertStringContainsString('BlobReference', $property);
    }

    public function test_it_handles_ref_type(): void
    {
        $property = $this->generator->generate(
            'author',
            [
                'type' => 'ref',
                'ref' => 'app.test.author',
            ],
            ['author']
        );

        $this->assertStringContainsString('App\\Lexicon\\Test\\App\\Author', $property);
    }

    public function test_it_generates_promoted_with_default(): void
    {
        $promoted = $this->generator->generatePromoted(
            'status',
            [
                'type' => 'string',
                'default' => 'pending',
            ],
            []
        );

        $this->assertStringContainsString('= \'pending\'', $promoted);
    }

    public function test_it_handles_number_type(): void
    {
        $property = $this->generator->generate('price', ['type' => 'number'], ['price']);

        $this->assertStringContainsString('float $price', $property);
    }

    public function test_it_handles_mixed_type(): void
    {
        $property = $this->generator->generate('data', ['type' => 'unknown'], ['data']);

        $this->assertStringContainsString('mixed $data', $property);
    }
}
