<?php

namespace SocialDept\Schema\Tests\Unit\Validation\TypeValidators;

use Orchestra\Testbench\TestCase;
use SocialDept\Schema\Exceptions\RecordValidationException;
use SocialDept\Schema\Validation\TypeValidators\BooleanValidator;

class BooleanValidatorTest extends TestCase
{
    protected BooleanValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new BooleanValidator();
    }

    public function test_it_validates_true(): void
    {
        $this->validator->validate(true, ['type' => 'boolean'], '$.field');

        $this->assertTrue(true);
    }

    public function test_it_validates_false(): void
    {
        $this->validator->validate(false, ['type' => 'boolean'], '$.field');

        $this->assertTrue(true);
    }

    public function test_it_rejects_non_boolean(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate('not a boolean', ['type' => 'boolean'], '$.field');
    }

    public function test_it_rejects_integer_zero(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate(0, ['type' => 'boolean'], '$.field');
    }

    public function test_it_validates_const_true(): void
    {
        $this->validator->validate(true, ['type' => 'boolean', 'const' => true], '$.field');

        $this->assertTrue(true);
    }

    public function test_it_validates_const_false(): void
    {
        $this->validator->validate(false, ['type' => 'boolean', 'const' => false], '$.field');

        $this->assertTrue(true);
    }

    public function test_it_rejects_value_not_matching_const(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate(false, ['type' => 'boolean', 'const' => true], '$.field');
    }
}
