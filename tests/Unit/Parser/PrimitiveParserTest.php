<?php

namespace SocialDept\Schema\Tests\Unit\Parser;

use Orchestra\Testbench\TestCase;
use SocialDept\Schema\Data\Types\BooleanType;
use SocialDept\Schema\Data\Types\BytesType;
use SocialDept\Schema\Data\Types\CidLinkType;
use SocialDept\Schema\Data\Types\IntegerType;
use SocialDept\Schema\Data\Types\NullType;
use SocialDept\Schema\Data\Types\StringType;
use SocialDept\Schema\Data\Types\UnknownType;
use SocialDept\Schema\Exceptions\TypeResolutionException;
use SocialDept\Schema\Parser\PrimitiveParser;

class PrimitiveParserTest extends TestCase
{
    protected PrimitiveParser $parser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = new PrimitiveParser();
    }

    public function test_it_parses_null_type(): void
    {
        $type = $this->parser->parse(['type' => 'null']);

        $this->assertInstanceOf(NullType::class, $type);
    }

    public function test_it_parses_boolean_type(): void
    {
        $type = $this->parser->parse(['type' => 'boolean']);

        $this->assertInstanceOf(BooleanType::class, $type);
    }

    public function test_it_parses_integer_type(): void
    {
        $type = $this->parser->parse(['type' => 'integer']);

        $this->assertInstanceOf(IntegerType::class, $type);
    }

    public function test_it_parses_string_type(): void
    {
        $type = $this->parser->parse(['type' => 'string']);

        $this->assertInstanceOf(StringType::class, $type);
    }

    public function test_it_parses_bytes_type(): void
    {
        $type = $this->parser->parse(['type' => 'bytes']);

        $this->assertInstanceOf(BytesType::class, $type);
    }

    public function test_it_parses_cid_link_type(): void
    {
        $type = $this->parser->parse(['type' => 'cid-link']);

        $this->assertInstanceOf(CidLinkType::class, $type);
    }

    public function test_it_parses_unknown_type(): void
    {
        $type = $this->parser->parse(['type' => 'unknown']);

        $this->assertInstanceOf(UnknownType::class, $type);
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

    public function test_it_checks_if_type_is_primitive(): void
    {
        $this->assertTrue($this->parser->isPrimitive('null'));
        $this->assertTrue($this->parser->isPrimitive('boolean'));
        $this->assertTrue($this->parser->isPrimitive('integer'));
        $this->assertTrue($this->parser->isPrimitive('string'));
        $this->assertTrue($this->parser->isPrimitive('bytes'));
        $this->assertTrue($this->parser->isPrimitive('cid-link'));
        $this->assertTrue($this->parser->isPrimitive('unknown'));

        $this->assertFalse($this->parser->isPrimitive('object'));
        $this->assertFalse($this->parser->isPrimitive('array'));
        $this->assertFalse($this->parser->isPrimitive('ref'));
    }

    public function test_it_returns_supported_types(): void
    {
        $types = $this->parser->getSupportedTypes();

        $this->assertCount(7, $types);
        $this->assertContains('null', $types);
        $this->assertContains('boolean', $types);
        $this->assertContains('integer', $types);
        $this->assertContains('string', $types);
        $this->assertContains('bytes', $types);
        $this->assertContains('cid-link', $types);
        $this->assertContains('unknown', $types);
    }

    public function test_it_parses_type_with_properties(): void
    {
        $type = $this->parser->parse([
            'type' => 'string',
            'description' => 'A test string',
            'minLength' => 1,
            'maxLength' => 100,
        ]);

        $this->assertInstanceOf(StringType::class, $type);
        $this->assertSame('A test string', $type->description);
        $this->assertSame(1, $type->minLength);
        $this->assertSame(100, $type->maxLength);
    }
}
