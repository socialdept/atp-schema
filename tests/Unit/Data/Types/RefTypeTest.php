<?php

namespace SocialDept\AtpSchema\Tests\Unit\Data\Types;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Data\Types\RefType;

class RefTypeTest extends TestCase
{
    public function test_it_creates_from_array(): void
    {
        $type = RefType::fromArray([
            'type' => 'ref',
            'ref' => '#main',
            'description' => 'A reference',
        ]);

        $this->assertSame('ref', $type->type);
        $this->assertSame('#main', $type->ref);
        $this->assertSame('A reference', $type->description);
    }

    public function test_it_throws_on_missing_ref(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('RefType requires a ref property');

        RefType::fromArray(['type' => 'ref']);
    }

    public function test_it_converts_to_array(): void
    {
        $type = new RefType(
            ref: '#main',
            description: 'A reference'
        );

        $array = $type->toArray();

        $this->assertSame('ref', $array['type']);
        $this->assertSame('#main', $array['ref']);
        $this->assertSame('A reference', $array['description']);
    }

    public function test_it_identifies_local_reference(): void
    {
        $type = new RefType('#main');

        $this->assertTrue($type->isLocal());
        $this->assertFalse($type->isExternal());
    }

    public function test_it_identifies_external_reference(): void
    {
        $type = new RefType('com.atproto.repo.strongRef');

        $this->assertFalse($type->isLocal());
        $this->assertTrue($type->isExternal());
    }

    public function test_it_gets_local_definition_name(): void
    {
        $type = new RefType('#main');

        $this->assertSame('main', $type->getLocalDefinition());
    }

    public function test_it_returns_null_for_external_definition(): void
    {
        $type = new RefType('com.atproto.repo.strongRef');

        $this->assertNull($type->getLocalDefinition());
    }

    public function test_it_validates_any_value(): void
    {
        $type = new RefType('#main');

        // Ref validation is deferred to higher-level validator
        $type->validate(['any' => 'value'], 'field');
        $type->validate('string', 'field');
        $type->validate(123, 'field');

        $this->assertTrue(true);
    }
}
