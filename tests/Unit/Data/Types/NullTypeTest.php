<?php

namespace SocialDept\AtpSchema\Tests\Unit\Data\Types;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Data\Types\NullType;
use SocialDept\AtpSchema\Exceptions\RecordValidationException;

class NullTypeTest extends TestCase
{
    public function test_it_creates_from_array(): void
    {
        $type = NullType::fromArray([
            'type' => 'null',
            'description' => 'A null value',
        ]);

        $this->assertSame('null', $type->type);
        $this->assertSame('A null value', $type->description);
    }

    public function test_it_converts_to_array(): void
    {
        $type = new NullType(description: 'A null value');

        $array = $type->toArray();

        $this->assertSame('null', $array['type']);
        $this->assertSame('A null value', $array['description']);
    }

    public function test_it_validates_null_type(): void
    {
        $type = new NullType();

        $type->validate(null, 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage("Expected type 'null' at 'field' but got 'string'");

        $type->validate('not null', 'field');
    }
}
