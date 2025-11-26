<?php

namespace SocialDept\AtpSchema\Tests\Unit\Validation;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Validation\ValidationError;

class ValidationErrorTest extends TestCase
{
    public function test_it_creates_simple_error(): void
    {
        $error = ValidationError::make('field', 'Field is required');

        $this->assertEquals('field', $error->getField());
        $this->assertEquals('Field is required', $error->getMessage());
        $this->assertNull($error->getRule());
    }

    public function test_it_creates_error_with_rule(): void
    {
        $error = ValidationError::withRule('field', 'Field is required', 'required');

        $this->assertEquals('field', $error->getField());
        $this->assertEquals('Field is required', $error->getMessage());
        $this->assertEquals('required', $error->getRule());
    }

    public function test_it_creates_error_with_full_context(): void
    {
        $error = ValidationError::withContext(
            'age',
            'Value exceeds maximum',
            'max',
            100,
            150
        );

        $this->assertEquals('age', $error->getField());
        $this->assertEquals('Value exceeds maximum', $error->getMessage());
        $this->assertEquals('max', $error->getRule());
        $this->assertEquals(100, $error->getExpected());
        $this->assertEquals(150, $error->getActual());
    }

    public function test_it_checks_if_has_rule(): void
    {
        $withRule = ValidationError::withRule('field', 'message', 'required');
        $withoutRule = ValidationError::make('field', 'message');

        $this->assertTrue($withRule->hasRule());
        $this->assertFalse($withoutRule->hasRule());
    }

    public function test_it_checks_if_has_expected(): void
    {
        $withExpected = ValidationError::withContext('field', 'message', 'max', 100);
        $withoutExpected = ValidationError::make('field', 'message');

        $this->assertTrue($withExpected->hasExpected());
        $this->assertFalse($withoutExpected->hasExpected());
    }

    public function test_it_checks_if_has_actual(): void
    {
        $withActual = ValidationError::withContext('field', 'message', 'type', 'string', 'integer');
        $withoutActual = ValidationError::make('field', 'message');

        $this->assertTrue($withActual->hasActual());
        $this->assertFalse($withoutActual->hasActual());
    }

    public function test_it_converts_to_array(): void
    {
        $error = ValidationError::withContext(
            'field',
            'message',
            'max',
            100,
            150,
            ['extra' => 'data']
        );

        $array = $error->toArray();

        $this->assertEquals('field', $array['field']);
        $this->assertEquals('message', $array['message']);
        $this->assertEquals('max', $array['rule']);
        $this->assertEquals(100, $array['expected']);
        $this->assertEquals(150, $array['actual']);
        $this->assertEquals(['extra' => 'data'], $array['context']);
    }

    public function test_it_converts_simple_error_to_array(): void
    {
        $error = ValidationError::make('field', 'message');

        $array = $error->toArray();

        $this->assertEquals(['field' => 'field', 'message' => 'message'], $array);
    }

    public function test_it_converts_to_json(): void
    {
        $error = ValidationError::withRule('field', 'message', 'required');

        $json = json_encode($error);
        $decoded = json_decode($json, true);

        $this->assertEquals('field', $decoded['field']);
        $this->assertEquals('message', $decoded['message']);
        $this->assertEquals('required', $decoded['rule']);
    }

    public function test_it_converts_to_string(): void
    {
        $error = ValidationError::make('username', 'Username is required');

        $this->assertEquals('username: Username is required', (string) $error);
    }

    public function test_it_stores_context_data(): void
    {
        $error = ValidationError::withContext(
            'field',
            'message',
            'rule',
            null,
            null,
            ['path' => '$.user.name', 'constraint' => 'maxLength']
        );

        $context = $error->getContext();

        $this->assertEquals('$.user.name', $context['path']);
        $this->assertEquals('maxLength', $context['constraint']);
    }

    public function test_it_handles_null_values(): void
    {
        $error = new ValidationError('field', 'message', null);

        $this->assertNull($error->getRule());
        $this->assertFalse($error->hasExpected());
        $this->assertFalse($error->hasActual());
        $this->assertEmpty($error->getContext());
    }

    public function test_it_handles_explicitly_null_values(): void
    {
        $error = new ValidationError('field', 'message', 'rule', null, null, []);

        $this->assertNull($error->getExpected());
        $this->assertNull($error->getActual());
        $this->assertTrue($error->hasExpected());
        $this->assertTrue($error->hasActual());
    }

    public function test_it_provides_readonly_access_to_properties(): void
    {
        $error = ValidationError::make('field', 'message');

        $this->assertEquals('field', $error->field);
        $this->assertEquals('message', $error->message);
        $this->assertNull($error->rule);
        $this->assertNull($error->expected);
        $this->assertNull($error->actual);
        $this->assertEmpty($error->context);
    }
}
