<?php

namespace SocialDept\Schema\Tests\Unit\Validation\TypeValidators;

use Orchestra\Testbench\TestCase;
use SocialDept\Schema\Exceptions\RecordValidationException;
use SocialDept\Schema\Validation\TypeValidators\ArrayValidator;

class ArrayValidatorTest extends TestCase
{
    protected ArrayValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new ArrayValidator();
    }

    public function test_it_validates_valid_array(): void
    {
        $this->validator->validate([1, 2, 3], ['type' => 'array'], '$.field');

        $this->assertTrue(true);
    }

    public function test_it_rejects_non_array(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate('not an array', ['type' => 'array'], '$.field');
    }

    public function test_it_validates_max_items(): void
    {
        $this->validator->validate([1, 2, 3], ['type' => 'array', 'maxItems' => 5], '$.field');

        $this->assertTrue(true);
    }

    public function test_it_rejects_array_exceeding_max_items(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate([1, 2, 3, 4, 5, 6], ['type' => 'array', 'maxItems' => 5], '$.field');
    }

    public function test_it_validates_min_items(): void
    {
        $this->validator->validate([1, 2, 3], ['type' => 'array', 'minItems' => 2], '$.field');

        $this->assertTrue(true);
    }

    public function test_it_rejects_array_below_min_items(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate([1], ['type' => 'array', 'minItems' => 3], '$.field');
    }

    public function test_it_validates_array_items(): void
    {
        $this->validator->validate(
            ['a', 'b', 'c'],
            ['type' => 'array', 'items' => ['type' => 'string']],
            '$.field'
        );

        $this->assertTrue(true);
    }

    public function test_it_rejects_invalid_array_item(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate(
            ['a', 123, 'c'],
            ['type' => 'array', 'items' => ['type' => 'string']],
            '$.field'
        );
    }

    public function test_it_validates_array_of_integers(): void
    {
        $this->validator->validate(
            [1, 2, 3],
            ['type' => 'array', 'items' => ['type' => 'integer']],
            '$.field'
        );

        $this->assertTrue(true);
    }

    public function test_it_validates_array_of_objects(): void
    {
        $this->validator->validate(
            [
                ['name' => 'John'],
                ['name' => 'Jane'],
            ],
            [
                'type' => 'array',
                'items' => [
                    'type' => 'object',
                    'required' => ['name'],
                    'properties' => [
                        'name' => ['type' => 'string'],
                    ],
                ],
            ],
            '$.field'
        );

        $this->assertTrue(true);
    }

    public function test_it_validates_nested_arrays(): void
    {
        $this->validator->validate(
            [[1, 2], [3, 4]],
            [
                'type' => 'array',
                'items' => [
                    'type' => 'array',
                    'items' => ['type' => 'integer'],
                ],
            ],
            '$.field'
        );

        $this->assertTrue(true);
    }
}
