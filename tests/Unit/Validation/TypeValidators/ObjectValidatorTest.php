<?php

namespace SocialDept\AtpSchema\Tests\Unit\Validation\TypeValidators;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Exceptions\RecordValidationException;
use SocialDept\AtpSchema\Validation\TypeValidators\ObjectValidator;

class ObjectValidatorTest extends TestCase
{
    protected ObjectValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new ObjectValidator();
    }

    public function test_it_validates_valid_object(): void
    {
        $this->validator->validate(
            ['name' => 'John'],
            [
                'type' => 'object',
                'required' => ['name'],
                'properties' => [
                    'name' => ['type' => 'string'],
                ],
            ],
            '$.field'
        );

        $this->assertTrue(true);
    }

    public function test_it_rejects_non_object(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate('not an object', ['type' => 'object'], '$.field');
    }

    public function test_it_validates_object_with_multiple_properties(): void
    {
        $this->validator->validate(
            ['name' => 'John', 'age' => 30],
            [
                'type' => 'object',
                'required' => ['name', 'age'],
                'properties' => [
                    'name' => ['type' => 'string'],
                    'age' => ['type' => 'integer'],
                ],
            ],
            '$.field'
        );

        $this->assertTrue(true);
    }

    public function test_it_rejects_missing_required_field(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate(
            ['name' => 'John'],
            [
                'type' => 'object',
                'required' => ['name', 'age'],
                'properties' => [
                    'name' => ['type' => 'string'],
                    'age' => ['type' => 'integer'],
                ],
            ],
            '$.field'
        );
    }

    public function test_it_validates_optional_properties(): void
    {
        $this->validator->validate(
            ['name' => 'John'],
            [
                'type' => 'object',
                'required' => ['name'],
                'properties' => [
                    'name' => ['type' => 'string'],
                    'age' => ['type' => 'integer'],
                ],
            ],
            '$.field'
        );

        $this->assertTrue(true);
    }

    public function test_it_validates_nested_objects(): void
    {
        $this->validator->validate(
            [
                'user' => [
                    'name' => 'John',
                    'profile' => [
                        'bio' => 'Developer',
                    ],
                ],
            ],
            [
                'type' => 'object',
                'required' => ['user'],
                'properties' => [
                    'user' => [
                        'type' => 'object',
                        'required' => ['name', 'profile'],
                        'properties' => [
                            'name' => ['type' => 'string'],
                            'profile' => [
                                'type' => 'object',
                                'required' => ['bio'],
                                'properties' => [
                                    'bio' => ['type' => 'string'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            '$.field'
        );

        $this->assertTrue(true);
    }

    public function test_it_rejects_invalid_property_type(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate(
            ['name' => 123],
            [
                'type' => 'object',
                'required' => ['name'],
                'properties' => [
                    'name' => ['type' => 'string'],
                ],
            ],
            '$.field'
        );
    }

    public function test_it_validates_empty_object(): void
    {
        $this->validator->validate(
            [],
            [
                'type' => 'object',
                'required' => [],
                'properties' => [],
            ],
            '$.field'
        );

        $this->assertTrue(true);
    }
}
