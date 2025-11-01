<?php

namespace SocialDept\Schema\Tests\Unit\Data\Types;

use Orchestra\Testbench\TestCase;
use SocialDept\Schema\Data\Types\IntegerType;
use SocialDept\Schema\Exceptions\RecordValidationException;

class IntegerTypeTest extends TestCase
{
    public function test_it_creates_from_array(): void
    {
        $type = IntegerType::fromArray([
            'type' => 'integer',
            'description' => 'An integer value',
            'minimum' => 1,
            'maximum' => 100,
            'enum' => [1, 2, 3],
            'const' => 5,
        ]);

        $this->assertSame('integer', $type->type);
        $this->assertSame('An integer value', $type->description);
        $this->assertSame(1, $type->minimum);
        $this->assertSame(100, $type->maximum);
        $this->assertSame([1, 2, 3], $type->enum);
        $this->assertSame(5, $type->const);
    }

    public function test_it_converts_to_array(): void
    {
        $type = new IntegerType(
            description: 'An integer value',
            minimum: 1,
            maximum: 100
        );

        $array = $type->toArray();

        $this->assertSame('integer', $array['type']);
        $this->assertSame('An integer value', $array['description']);
        $this->assertSame(1, $array['minimum']);
        $this->assertSame(100, $array['maximum']);
    }

    public function test_it_validates_integer_type(): void
    {
        $type = new IntegerType();

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage("Expected type 'integer' at 'field' but got 'string'");

        $type->validate('123', 'field');
    }

    public function test_it_validates_const(): void
    {
        $type = new IntegerType(const: 5);

        $type->validate(5, 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must equal 5');

        $type->validate(10, 'field');
    }

    public function test_it_validates_enum(): void
    {
        $type = new IntegerType(enum: [1, 2, 3]);

        $type->validate(1, 'field');
        $type->validate(2, 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must be one of: 1, 2, 3');

        $type->validate(5, 'field');
    }

    public function test_it_validates_minimum(): void
    {
        $type = new IntegerType(minimum: 10);

        $type->validate(10, 'field');
        $type->validate(20, 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must be at least 10');

        $type->validate(5, 'field');
    }

    public function test_it_validates_maximum(): void
    {
        $type = new IntegerType(maximum: 10);

        $type->validate(10, 'field');
        $type->validate(5, 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must be at most 10');

        $type->validate(20, 'field');
    }
}
