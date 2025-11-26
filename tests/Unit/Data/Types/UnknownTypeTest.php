<?php

namespace SocialDept\AtpSchema\Tests\Unit\Data\Types;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Data\Types\UnknownType;

class UnknownTypeTest extends TestCase
{
    public function test_it_creates_from_array(): void
    {
        $type = UnknownType::fromArray([
            'type' => 'unknown',
            'description' => 'An unknown value',
        ]);

        $this->assertSame('unknown', $type->type);
        $this->assertSame('An unknown value', $type->description);
    }

    public function test_it_converts_to_array(): void
    {
        $type = new UnknownType(description: 'An unknown value');

        $array = $type->toArray();

        $this->assertSame('unknown', $array['type']);
        $this->assertSame('An unknown value', $array['description']);
    }

    public function test_it_accepts_any_value(): void
    {
        $type = new UnknownType();

        // Unknown type should accept any value without throwing
        $type->validate('string', 'field');
        $type->validate(123, 'field');
        $type->validate(true, 'field');
        $type->validate(['array'], 'field');
        $type->validate(null, 'field');

        $this->assertTrue(true); // If we get here, validation passed
    }
}
