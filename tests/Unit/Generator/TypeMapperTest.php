<?php

namespace SocialDept\Schema\Tests\Unit\Generator;

use Orchestra\Testbench\TestCase;
use SocialDept\Schema\Generator\NamingConverter;
use SocialDept\Schema\Generator\TypeMapper;

class TypeMapperTest extends TestCase
{
    protected TypeMapper $mapper;

    protected function setUp(): void
    {
        parent::setUp();

        $naming = new NamingConverter('App\\Lexicon');
        $this->mapper = new TypeMapper($naming);
    }

    public function test_it_maps_string_type(): void
    {
        $type = $this->mapper->toPhpType(['type' => 'string']);

        $this->assertSame('string', $type);
    }

    public function test_it_maps_integer_type(): void
    {
        $type = $this->mapper->toPhpType(['type' => 'integer']);

        $this->assertSame('int', $type);
    }

    public function test_it_maps_boolean_type(): void
    {
        $type = $this->mapper->toPhpType(['type' => 'boolean']);

        $this->assertSame('bool', $type);
    }

    public function test_it_maps_number_type(): void
    {
        $type = $this->mapper->toPhpType(['type' => 'number']);

        $this->assertSame('float', $type);
    }

    public function test_it_maps_array_type(): void
    {
        $type = $this->mapper->toPhpType(['type' => 'array']);

        $this->assertSame('array', $type);
    }

    public function test_it_maps_object_type(): void
    {
        $type = $this->mapper->toPhpType(['type' => 'object']);

        $this->assertSame('array', $type);
    }

    public function test_it_maps_blob_type(): void
    {
        $type = $this->mapper->toPhpType(['type' => 'blob']);

        $this->assertSame('BlobReference', $type);
    }

    public function test_it_maps_bytes_type(): void
    {
        $type = $this->mapper->toPhpType(['type' => 'bytes']);

        $this->assertSame('string', $type);
    }

    public function test_it_maps_cid_link_type(): void
    {
        $type = $this->mapper->toPhpType(['type' => 'cid-link']);

        $this->assertSame('string', $type);
    }

    public function test_it_maps_unknown_type(): void
    {
        $type = $this->mapper->toPhpType(['type' => 'unknown']);

        $this->assertSame('mixed', $type);
    }

    public function test_it_maps_ref_type(): void
    {
        $type = $this->mapper->toPhpType([
            'type' => 'ref',
            'ref' => 'app.bsky.feed.post',
        ]);

        $this->assertSame('Post', $type);
    }

    public function test_it_maps_union_type(): void
    {
        $type = $this->mapper->toPhpType(['type' => 'union']);

        $this->assertSame('mixed', $type);
    }

    public function test_it_handles_nullable_types(): void
    {
        $type = $this->mapper->toPhpType(['type' => 'string'], true);

        $this->assertSame('?string', $type);
    }

    public function test_it_does_not_make_mixed_nullable(): void
    {
        $type = $this->mapper->toPhpType(['type' => 'unknown'], true);

        $this->assertSame('mixed', $type);
    }

    public function test_it_maps_array_doc_type_with_items(): void
    {
        $docType = $this->mapper->toPhpDocType([
            'type' => 'array',
            'items' => ['type' => 'string'],
        ]);

        $this->assertSame('array<string>', $docType);
    }

    public function test_it_maps_array_doc_type_without_items(): void
    {
        $docType = $this->mapper->toPhpDocType(['type' => 'array']);

        $this->assertSame('array', $docType);
    }

    public function test_it_maps_object_doc_type_with_properties(): void
    {
        $docType = $this->mapper->toPhpDocType([
            'type' => 'object',
            'properties' => [
                'name' => ['type' => 'string'],
                'age' => ['type' => 'integer'],
            ],
        ]);

        $this->assertSame('array{name: string, age: int}', $docType);
    }

    public function test_it_maps_object_doc_type_without_properties(): void
    {
        $docType = $this->mapper->toPhpDocType(['type' => 'object']);

        $this->assertSame('array', $docType);
    }

    public function test_it_maps_union_doc_type(): void
    {
        $docType = $this->mapper->toPhpDocType([
            'type' => 'union',
            'refs' => [
                'app.bsky.feed.post',
                'app.bsky.feed.repost',
            ],
        ]);

        $this->assertSame('mixed', $docType);
    }

    public function test_it_adds_null_to_doc_type_when_nullable(): void
    {
        $docType = $this->mapper->toPhpDocType(['type' => 'string'], true);

        $this->assertSame('string|null', $docType);
    }

    public function test_it_checks_if_type_is_nullable(): void
    {
        $this->assertFalse($this->mapper->isNullable(['required' => true]));
        $this->assertFalse($this->mapper->isNullable(['name' => 'field'], ['field']));
        $this->assertTrue($this->mapper->isNullable([]));
    }

    public function test_it_gets_string_default_value(): void
    {
        $default = $this->mapper->getDefaultValue(['default' => 'hello']);

        $this->assertSame("'hello'", $default);
    }

    public function test_it_gets_boolean_default_value(): void
    {
        $this->assertSame('true', $this->mapper->getDefaultValue(['default' => true]));
        $this->assertSame('false', $this->mapper->getDefaultValue(['default' => false]));
    }

    public function test_it_gets_numeric_default_value(): void
    {
        $this->assertSame('42', $this->mapper->getDefaultValue(['default' => 42]));
        $this->assertSame('3.14', $this->mapper->getDefaultValue(['default' => 3.14]));
    }

    public function test_it_gets_array_default_value(): void
    {
        $default = $this->mapper->getDefaultValue(['default' => []]);

        $this->assertSame('[]', $default);
    }

    public function test_it_gets_null_default_value(): void
    {
        $default = $this->mapper->getDefaultValue(['default' => null]);

        $this->assertSame('null', $default);
    }

    public function test_it_returns_null_when_no_default(): void
    {
        $default = $this->mapper->getDefaultValue([]);

        $this->assertNull($default);
    }

    public function test_it_checks_if_type_needs_use_statement(): void
    {
        $this->assertTrue($this->mapper->needsUseStatement(['type' => 'ref']));
        $this->assertTrue($this->mapper->needsUseStatement(['type' => 'blob']));
        $this->assertFalse($this->mapper->needsUseStatement(['type' => 'string']));
    }

    public function test_it_gets_use_statements_for_blob(): void
    {
        $uses = $this->mapper->getUseStatements(['type' => 'blob']);

        $this->assertContains('SocialDept\\Schema\\Data\\BlobReference', $uses);
    }

    public function test_it_gets_use_statements_for_ref(): void
    {
        $uses = $this->mapper->getUseStatements([
            'type' => 'ref',
            'ref' => 'app.bsky.feed.post',
        ]);

        $this->assertContains('App\\Lexicon\\App\\Bsky\\Feed\\Post', $uses);
    }

    public function test_it_gets_use_statements_for_open_union(): void
    {
        $uses = $this->mapper->getUseStatements([
            'type' => 'union',
            'refs' => [
                'app.bsky.feed.post',
                'app.bsky.feed.repost',
            ],
        ]);

        $this->assertEmpty($uses);
    }

    public function test_it_gets_use_statements_for_closed_union(): void
    {
        $uses = $this->mapper->getUseStatements([
            'type' => 'union',
            'closed' => true,
            'refs' => [
                'app.bsky.feed.post',
                'app.bsky.feed.repost',
            ],
        ]);

        $this->assertCount(2, $uses);
        $this->assertContains('App\\Lexicon\\App\\Bsky\\Feed\\Post', $uses);
        $this->assertContains('App\\Lexicon\\App\\Bsky\\Feed\\Repost', $uses);
    }

    public function test_it_gets_empty_use_statements_for_primitive(): void
    {
        $uses = $this->mapper->getUseStatements(['type' => 'string']);

        $this->assertEmpty($uses);
    }

    public function test_it_escapes_quotes_in_string_defaults(): void
    {
        $default = $this->mapper->getDefaultValue(['default' => "it's great"]);

        $this->assertSame("'it\\'s great'", $default);
    }

    public function test_it_handles_missing_type(): void
    {
        $type = $this->mapper->toPhpType([]);

        $this->assertSame('mixed', $type);
    }
}
