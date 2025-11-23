<?php

namespace SocialDept\Schema\Tests\Unit\Data\Types;

use Orchestra\Testbench\TestCase;
use SocialDept\Schema\Data\Types\ObjectType;
use SocialDept\Schema\Data\Types\StringType;
use SocialDept\Schema\Exceptions\RecordValidationException;

class ObjectTypeTest extends TestCase
{
    public function test_it_creates_from_array(): void
    {
        $type = ObjectType::fromArray([
            'type' => 'object',
            'description' => 'An object',
            'required' => ['name'],
            'nullable' => true,
        ]);

        $this->assertSame('object', $type->type);
        $this->assertSame('An object', $type->description);
        $this->assertSame(['name'], $type->required);
        $this->assertTrue($type->nullable);
    }

    public function test_it_converts_to_array(): void
    {
        $type = new ObjectType(
            properties: ['name' => new StringType()],
            required: ['name'],
            nullable: true,
            description: 'An object'
        );

        $array = $type->toArray();

        $this->assertSame('object', $array['type']);
        $this->assertSame('An object', $array['description']);
        $this->assertArrayHasKey('properties', $array);
        $this->assertSame(['name'], $array['required']);
        $this->assertTrue($array['nullable']);
    }

    public function test_it_validates_object_type(): void
    {
        $type = new ObjectType();

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage("Expected type 'object' at 'field' but got 'string'");

        $type->validate('not an object', 'field');
    }

    public function test_it_validates_required_properties(): void
    {
        $type = new ObjectType(
            properties: ['name' => new StringType()],
            required: ['name']
        );

        $type->validate(['name' => 'John'], 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage("Invalid value at 'field': missing required property 'name'");

        $type->validate([], 'field');
    }

    public function test_it_validates_property_types(): void
    {
        $type = new ObjectType(
            properties: ['name' => new StringType()]
        );

        $type->validate(['name' => 'John'], 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage("Expected type 'string' at 'field.name' but got 'integer'");

        $type->validate(['name' => 123], 'field');
    }

    public function test_it_allows_nullable_properties(): void
    {
        $type = new ObjectType(
            properties: ['name' => new StringType()],
            nullable: true
        );

        $type->validate(['name' => null], 'field');

        $this->assertTrue(true);
    }

    public function test_it_allows_additional_properties(): void
    {
        $type = new ObjectType(
            properties: ['name' => new StringType()]
        );

        // Additional properties should be allowed
        $type->validate(['name' => 'John', 'age' => 30], 'field');

        $this->assertTrue(true);
    }

    public function test_with_properties_returns_new_instance(): void
    {
        $type = new ObjectType(required: ['name']);

        $newType = $type->withProperties(['name' => new StringType()]);

        $this->assertNotSame($type, $newType);
        $this->assertEmpty($type->properties);
        $this->assertCount(1, $newType->properties);
    }
}
