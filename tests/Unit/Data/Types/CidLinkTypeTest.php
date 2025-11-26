<?php

namespace SocialDept\AtpSchema\Tests\Unit\Data\Types;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Data\Types\CidLinkType;
use SocialDept\AtpSchema\Exceptions\RecordValidationException;

class CidLinkTypeTest extends TestCase
{
    public function test_it_creates_from_array(): void
    {
        $type = CidLinkType::fromArray([
            'type' => 'cid-link',
            'description' => 'A CID link',
        ]);

        $this->assertSame('cid-link', $type->type);
        $this->assertSame('A CID link', $type->description);
    }

    public function test_it_converts_to_array(): void
    {
        $type = new CidLinkType(description: 'A CID link');

        $array = $type->toArray();

        $this->assertSame('cid-link', $array['type']);
        $this->assertSame('A CID link', $array['description']);
    }

    public function test_it_validates_cid_link_type(): void
    {
        $type = new CidLinkType();

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage("Expected type 'cid-link (object with \$link)' at 'field' but got 'string'");

        $type->validate('not an object', 'field');
    }

    public function test_it_validates_valid_cid_link(): void
    {
        $type = new CidLinkType();

        $type->validate(['$link' => 'bafyreihqhqv7h2gfxkj7qxvz7pxqhqvz7h2gfxkj7'], 'field');

        $this->assertTrue(true);
    }

    public function test_it_rejects_missing_link_property(): void
    {
        $type = new CidLinkType();

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must contain $link property');

        $type->validate(['other' => 'value'], 'field');
    }

    public function test_it_rejects_non_string_link(): void
    {
        $type = new CidLinkType();

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': $link must be a string');

        $type->validate(['$link' => 123], 'field');
    }

    public function test_it_rejects_invalid_cid_format(): void
    {
        $type = new CidLinkType();

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': $link must be a valid CID');

        $type->validate(['$link' => 'invalid cid!'], 'field');
    }
}
