<?php

namespace SocialDept\AtpSchema\Tests\Unit\Parser;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Data\Types\ArrayType;
use SocialDept\AtpSchema\Data\Types\BlobType;
use SocialDept\AtpSchema\Data\Types\ObjectType;
use SocialDept\AtpSchema\Data\Types\RefType;
use SocialDept\AtpSchema\Data\Types\StringType;
use SocialDept\AtpSchema\Data\Types\UnionType;
use SocialDept\AtpSchema\Exceptions\TypeResolutionException;
use SocialDept\AtpSchema\Parser\ComplexTypeParser;

class ComplexTypeParserTest extends TestCase
{
    protected ComplexTypeParser $parser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = new ComplexTypeParser();
    }

    public function test_it_parses_object_type(): void
    {
        $type = $this->parser->parse(['type' => 'object']);

        $this->assertInstanceOf(ObjectType::class, $type);
    }

    public function test_it_parses_object_with_properties(): void
    {
        $type = $this->parser->parse([
            'type' => 'object',
            'properties' => [
                'name' => ['type' => 'string'],
                'age' => ['type' => 'integer'],
            ],
        ]);

        $this->assertInstanceOf(ObjectType::class, $type);
        $this->assertCount(2, $type->properties);
        $this->assertInstanceOf(StringType::class, $type->properties['name']);
    }

    public function test_it_parses_array_type(): void
    {
        $type = $this->parser->parse(['type' => 'array']);

        $this->assertInstanceOf(ArrayType::class, $type);
    }

    public function test_it_parses_array_with_items(): void
    {
        $type = $this->parser->parse([
            'type' => 'array',
            'items' => ['type' => 'string'],
        ]);

        $this->assertInstanceOf(ArrayType::class, $type);
        $this->assertInstanceOf(StringType::class, $type->items);
    }

    public function test_it_parses_nested_complex_types(): void
    {
        $type = $this->parser->parse([
            'type' => 'object',
            'properties' => [
                'items' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                ],
            ],
        ]);

        $this->assertInstanceOf(ObjectType::class, $type);
        $this->assertInstanceOf(ArrayType::class, $type->properties['items']);
        $this->assertInstanceOf(StringType::class, $type->properties['items']->items);
    }

    public function test_it_parses_union_type(): void
    {
        $type = $this->parser->parse([
            'type' => 'union',
            'refs' => ['#typeA', '#typeB'],
        ]);

        $this->assertInstanceOf(UnionType::class, $type);
    }

    public function test_it_parses_ref_type(): void
    {
        $type = $this->parser->parse([
            'type' => 'ref',
            'ref' => '#main',
        ]);

        $this->assertInstanceOf(RefType::class, $type);
    }

    public function test_it_parses_blob_type(): void
    {
        $type = $this->parser->parse([
            'type' => 'blob',
            'accept' => ['image/*'],
        ]);

        $this->assertInstanceOf(BlobType::class, $type);
    }

    public function test_it_throws_on_missing_type(): void
    {
        $this->expectException(TypeResolutionException::class);
        $this->expectExceptionMessage('Unknown Lexicon type: (missing type field)');

        $this->parser->parse([]);
    }

    public function test_it_throws_on_unknown_type(): void
    {
        $this->expectException(TypeResolutionException::class);
        $this->expectExceptionMessage('Unknown Lexicon type: nonexistent');

        $this->parser->parse(['type' => 'nonexistent']);
    }

    public function test_it_checks_if_type_is_complex(): void
    {
        $this->assertTrue($this->parser->isComplex('object'));
        $this->assertTrue($this->parser->isComplex('array'));
        $this->assertTrue($this->parser->isComplex('union'));
        $this->assertTrue($this->parser->isComplex('ref'));
        $this->assertTrue($this->parser->isComplex('blob'));

        $this->assertFalse($this->parser->isComplex('string'));
        $this->assertFalse($this->parser->isComplex('integer'));
    }

    public function test_it_returns_supported_types(): void
    {
        $types = $this->parser->getSupportedTypes();

        $this->assertCount(5, $types);
        $this->assertContains('object', $types);
        $this->assertContains('array', $types);
        $this->assertContains('union', $types);
        $this->assertContains('ref', $types);
        $this->assertContains('blob', $types);
    }
}
