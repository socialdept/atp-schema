<?php

namespace SocialDept\Schema\Tests\Unit\Validation\TypeValidators;

use Orchestra\Testbench\TestCase;
use SocialDept\Schema\Exceptions\RecordValidationException;
use SocialDept\Schema\Validation\TypeValidators\IntegerValidator;

class IntegerValidatorTest extends TestCase
{
    protected IntegerValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new IntegerValidator();
    }

    public function test_it_validates_valid_integer(): void
    {
        $this->validator->validate(42, ['type' => 'integer'], '$.field');

        $this->assertTrue(true);
    }

    public function test_it_rejects_non_integer(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate('not an integer', ['type' => 'integer'], '$.field');
    }

    public function test_it_validates_maximum_constraint(): void
    {
        $this->validator->validate(50, ['type' => 'integer', 'maximum' => 100], '$.field');

        $this->assertTrue(true);
    }

    public function test_it_rejects_value_exceeding_maximum(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate(150, ['type' => 'integer', 'maximum' => 100], '$.field');
    }

    public function test_it_validates_minimum_constraint(): void
    {
        $this->validator->validate(50, ['type' => 'integer', 'minimum' => 10], '$.field');

        $this->assertTrue(true);
    }

    public function test_it_rejects_value_below_minimum(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate(5, ['type' => 'integer', 'minimum' => 10], '$.field');
    }

    public function test_it_validates_enum_constraint(): void
    {
        $this->validator->validate(2, ['type' => 'integer', 'enum' => [1, 2, 3]], '$.field');

        $this->assertTrue(true);
    }

    public function test_it_rejects_value_not_in_enum(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate(5, ['type' => 'integer', 'enum' => [1, 2, 3]], '$.field');
    }

    public function test_it_validates_const_constraint(): void
    {
        $this->validator->validate(42, ['type' => 'integer', 'const' => 42], '$.field');

        $this->assertTrue(true);
    }

    public function test_it_rejects_value_not_matching_const(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate(100, ['type' => 'integer', 'const' => 42], '$.field');
    }
}
