<?php

namespace SocialDept\AtpSchema\Tests\Unit\Data\Types;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Data\Types\ArrayType;
use SocialDept\AtpSchema\Data\Types\StringType;
use SocialDept\AtpSchema\Exceptions\RecordValidationException;

class ArrayTypeTest extends TestCase
{
    public function test_it_creates_from_array(): void
    {
        $type = ArrayType::fromArray([
            'type' => 'array',
            'description' => 'An array',
            'minLength' => 1,
            'maxLength' => 10,
        ]);

        $this->assertSame('array', $type->type);
        $this->assertSame('An array', $type->description);
        $this->assertSame(1, $type->minLength);
        $this->assertSame(10, $type->maxLength);
    }

    public function test_it_converts_to_array(): void
    {
        $type = new ArrayType(
            items: new StringType(),
            minLength: 1,
            maxLength: 10,
            description: 'An array'
        );

        $array = $type->toArray();

        $this->assertSame('array', $array['type']);
        $this->assertSame('An array', $array['description']);
        $this->assertArrayHasKey('items', $array);
        $this->assertSame(1, $array['minLength']);
        $this->assertSame(10, $array['maxLength']);
    }

    public function test_it_validates_array_type(): void
    {
        $type = new ArrayType();

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage("Expected type 'array' at 'field' but got 'string'");

        $type->validate('not an array', 'field');
    }

    public function test_it_validates_sequential_array(): void
    {
        $type = new ArrayType();

        $type->validate(['a', 'b', 'c'], 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must be a sequential array');

        $type->validate(['key' => 'value'], 'field');
    }

    public function test_it_validates_min_length(): void
    {
        $type = new ArrayType(minLength: 2);

        $type->validate(['a', 'b'], 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must have at least 2 items');

        $type->validate(['a'], 'field');
    }

    public function test_it_validates_max_length(): void
    {
        $type = new ArrayType(maxLength: 2);

        $type->validate(['a', 'b'], 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must have at most 2 items');

        $type->validate(['a', 'b', 'c'], 'field');
    }

    public function test_it_validates_item_types(): void
    {
        $type = new ArrayType(items: new StringType());

        $type->validate(['a', 'b', 'c'], 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage("Expected type 'string' at 'field[1]' but got 'integer'");

        $type->validate(['a', 123, 'c'], 'field');
    }

    public function test_with_items_returns_new_instance(): void
    {
        $type = new ArrayType(minLength: 1);

        $newType = $type->withItems(new StringType());

        $this->assertNotSame($type, $newType);
        $this->assertNull($type->items);
        $this->assertInstanceOf(StringType::class, $newType->items);
    }
}
