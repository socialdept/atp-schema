<?php

namespace SocialDept\Schema\Tests\Unit\Data\Types;

use Orchestra\Testbench\TestCase;
use SocialDept\Schema\Data\Types\BooleanType;
use SocialDept\Schema\Exceptions\RecordValidationException;

class BooleanTypeTest extends TestCase
{
    public function test_it_creates_from_array(): void
    {
        $type = BooleanType::fromArray([
            'type' => 'boolean',
            'description' => 'A boolean value',
            'const' => true,
        ]);

        $this->assertSame('boolean', $type->type);
        $this->assertSame('A boolean value', $type->description);
        $this->assertTrue($type->const);
    }

    public function test_it_converts_to_array(): void
    {
        $type = new BooleanType(
            description: 'A boolean value',
            const: false
        );

        $array = $type->toArray();

        $this->assertSame('boolean', $array['type']);
        $this->assertSame('A boolean value', $array['description']);
        $this->assertFalse($array['const']);
    }

    public function test_it_validates_boolean_type(): void
    {
        $type = new BooleanType();

        $type->validate(true, 'field');
        $type->validate(false, 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage("Expected type 'boolean' at 'field' but got 'string'");

        $type->validate('true', 'field');
    }

    public function test_it_validates_const_true(): void
    {
        $type = new BooleanType(const: true);

        $type->validate(true, 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must equal true');

        $type->validate(false, 'field');
    }

    public function test_it_validates_const_false(): void
    {
        $type = new BooleanType(const: false);

        $type->validate(false, 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must equal false');

        $type->validate(true, 'field');
    }
}
