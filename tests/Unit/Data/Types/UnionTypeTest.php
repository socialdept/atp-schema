<?php

namespace SocialDept\Schema\Tests\Unit\Data\Types;

use Orchestra\Testbench\TestCase;
use SocialDept\Schema\Data\Types\UnionType;
use SocialDept\Schema\Exceptions\RecordValidationException;

class UnionTypeTest extends TestCase
{
    public function test_it_creates_from_array(): void
    {
        $type = UnionType::fromArray([
            'type' => 'union',
            'description' => 'A union',
            'refs' => ['#typeA', '#typeB'],
            'closed' => true,
        ]);

        $this->assertSame('union', $type->type);
        $this->assertSame('A union', $type->description);
        $this->assertSame(['#typeA', '#typeB'], $type->refs);
        $this->assertTrue($type->closed);
    }

    public function test_it_converts_to_array(): void
    {
        $type = new UnionType(
            refs: ['#typeA', '#typeB'],
            closed: true,
            description: 'A union'
        );

        $array = $type->toArray();

        $this->assertSame('union', $array['type']);
        $this->assertSame('A union', $array['description']);
        $this->assertSame(['#typeA', '#typeB'], $array['refs']);
        $this->assertTrue($array['closed']);
    }

    public function test_it_validates_union_type(): void
    {
        $type = new UnionType();

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage("Expected type 'union (object with \$type)' at 'field' but got 'string'");

        $type->validate('not an object', 'field');
    }

    public function test_it_validates_type_discriminator(): void
    {
        $type = new UnionType();

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must contain $type property');

        $type->validate(['data' => 'value'], 'field');
    }

    public function test_it_validates_type_discriminator_is_string(): void
    {
        $type = new UnionType();

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': $type must be a string');

        $type->validate(['$type' => 123], 'field');
    }

    public function test_it_validates_closed_union(): void
    {
        $type = new UnionType(
            refs: ['#typeA', '#typeB'],
            closed: true
        );

        $type->validate(['$type' => '#typeA'], 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': type must be one of: #typeA, #typeB');

        $type->validate(['$type' => '#typeC'], 'field');
    }

    public function test_it_allows_any_type_in_open_union(): void
    {
        $type = new UnionType(
            refs: ['#typeA', '#typeB'],
            closed: false
        );

        $type->validate(['$type' => '#typeC'], 'field');

        $this->assertTrue(true);
    }
}
