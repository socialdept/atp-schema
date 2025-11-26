<?php

namespace SocialDept\AtpSchema\Tests\Unit\Validation;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Validation\ValidationError;
use SocialDept\AtpSchema\Validation\ValidationErrorFormatter;

class ValidationErrorFormatterTest extends TestCase
{
    protected ValidationErrorFormatter $formatter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formatter = new ValidationErrorFormatter();
    }

    public function test_it_formats_for_laravel(): void
    {
        $errors = [
            ValidationError::make('name', 'Name is required'),
            ValidationError::make('email', 'Email is invalid'),
        ];

        $formatted = $this->formatter->formatForLaravel($errors);

        $this->assertEquals([
            'name' => ['Name is required'],
            'email' => ['Email is invalid'],
        ], $formatted);
    }

    public function test_it_groups_multiple_errors_per_field(): void
    {
        $errors = [
            ValidationError::make('name', 'Name is required'),
            ValidationError::make('name', 'Name must be a string'),
        ];

        $formatted = $this->formatter->formatForLaravel($errors);

        $this->assertEquals([
            'name' => ['Name is required', 'Name must be a string'],
        ], $formatted);
    }

    public function test_it_converts_field_paths(): void
    {
        $errors = [
            ValidationError::make('$.user.name', 'Invalid name'),
            ValidationError::make('$.items[0]', 'Invalid item'),
        ];

        $formatted = $this->formatter->formatForLaravel($errors);

        $this->assertArrayHasKey('user.name', $formatted);
        $this->assertArrayHasKey('items.0', $formatted);
    }

    public function test_it_handles_root_field(): void
    {
        $errors = [
            ValidationError::make('$', 'Invalid data'),
        ];

        $formatted = $this->formatter->formatForLaravel($errors);

        $this->assertArrayHasKey('_root', $formatted);
    }

    public function test_it_formats_as_messages(): void
    {
        $errors = [
            ValidationError::make('name', 'Name is required'),
            ValidationError::make('email', 'Email is invalid'),
        ];

        $messages = $this->formatter->formatAsMessages($errors);

        $this->assertEquals(['Name is required', 'Email is invalid'], $messages);
    }

    public function test_it_formats_with_fields(): void
    {
        $errors = [
            ValidationError::make('name', 'Name is required'),
            ValidationError::make('email', 'Email is invalid'),
        ];

        $messages = $this->formatter->formatWithFields($errors);

        $this->assertEquals([
            'name: Name is required',
            'email: Email is invalid',
        ], $messages);
    }

    public function test_it_formats_detailed(): void
    {
        $errors = [
            ValidationError::withContext('age', 'Too high', 'max', 100, 150),
        ];

        $detailed = $this->formatter->formatDetailed($errors);

        $this->assertEquals([
            [
                'field' => 'age',
                'message' => 'Too high',
                'rule' => 'max',
                'expected' => 100,
                'actual' => 150,
            ],
        ], $detailed);
    }

    public function test_it_groups_by_field(): void
    {
        $errors = [
            ValidationError::make('name', 'Required'),
            ValidationError::make('name', 'Must be string'),
            ValidationError::make('email', 'Invalid'),
        ];

        $grouped = $this->formatter->groupByField($errors);

        $this->assertCount(2, $grouped);
        $this->assertCount(2, $grouped['name']);
        $this->assertCount(1, $grouped['email']);
    }

    public function test_it_formats_single_error(): void
    {
        $error = ValidationError::withContext('age', 'Too high', 'max', 100, 150);

        $formatted = $this->formatter->formatError($error);

        $this->assertStringContainsString('Too high', $formatted);
        $this->assertStringContainsString('Rule: max', $formatted);
        $this->assertStringContainsString('Expected: 100', $formatted);
        $this->assertStringContainsString('Got: 150', $formatted);
    }

    public function test_it_formats_error_without_context(): void
    {
        $error = ValidationError::make('name', 'Required');

        $formatted = $this->formatter->formatError($error);

        $this->assertEquals('Required', $formatted);
    }

    public function test_it_creates_summary_for_no_errors(): void
    {
        $summary = $this->formatter->createSummary([]);

        $this->assertEquals('No validation errors', $summary);
    }

    public function test_it_creates_summary_for_single_error(): void
    {
        $errors = [
            ValidationError::make('name', 'Name is required'),
        ];

        $summary = $this->formatter->createSummary($errors);

        $this->assertEquals('Validation failed: Name is required', $summary);
    }

    public function test_it_creates_summary_for_multiple_errors(): void
    {
        $errors = [
            ValidationError::make('name', 'Required'),
            ValidationError::make('email', 'Invalid'),
        ];

        $summary = $this->formatter->createSummary($errors);

        $this->assertStringContainsString('2 errors', $summary);
        $this->assertStringContainsString('2 fields', $summary);
    }

    public function test_it_creates_summary_for_multiple_errors_same_field(): void
    {
        $errors = [
            ValidationError::make('name', 'Required'),
            ValidationError::make('name', 'Must be string'),
        ];

        $summary = $this->formatter->createSummary($errors);

        $this->assertStringContainsString('2 errors', $summary);
        $this->assertStringContainsString('1 fields', $summary);
    }

    public function test_it_converts_to_json(): void
    {
        $errors = [
            ValidationError::withRule('name', 'Required', 'required'),
        ];

        $json = $this->formatter->toJson($errors);
        $decoded = json_decode($json, true);

        $this->assertCount(1, $decoded);
        $this->assertEquals('name', $decoded[0]['field']);
        $this->assertEquals('Required', $decoded[0]['message']);
    }

    public function test_it_converts_to_pretty_json(): void
    {
        $errors = [
            ValidationError::make('name', 'Required'),
        ];

        $json = $this->formatter->toPrettyJson($errors);

        $this->assertStringContainsString("\n", $json);
        $this->assertStringContainsString('    ', $json);
    }

    public function test_it_formats_null_value(): void
    {
        $error = ValidationError::withContext('field', 'message', 'type', 'string', null);

        $formatted = $this->formatter->formatError($error);

        $this->assertStringContainsString('Got: null', $formatted);
    }

    public function test_it_formats_boolean_values(): void
    {
        $error = ValidationError::withContext('field', 'message', 'type', true, false);

        $formatted = $this->formatter->formatError($error);

        $this->assertStringContainsString('Expected: true', $formatted);
        $this->assertStringContainsString('Got: false', $formatted);
    }

    public function test_it_formats_array_value(): void
    {
        $error = ValidationError::withContext('field', 'message', 'type', 'string', [1, 2, 3]);

        $formatted = $this->formatter->formatError($error);

        $this->assertStringContainsString('Got: array(3)', $formatted);
    }

    public function test_it_formats_long_string(): void
    {
        $longString = str_repeat('a', 100);
        $error = ValidationError::withContext('field', 'message', 'type', 'short', $longString);

        $formatted = $this->formatter->formatError($error);

        $this->assertStringContainsString('...', $formatted);
    }

    public function test_it_handles_empty_errors_array(): void
    {
        $formatted = $this->formatter->formatForLaravel([]);

        $this->assertEmpty($formatted);
    }

    public function test_it_formats_nested_field_paths(): void
    {
        $errors = [
            ValidationError::make('$.user.profile.bio', 'Too long'),
        ];

        $formatted = $this->formatter->formatForLaravel($errors);

        $this->assertArrayHasKey('user.profile.bio', $formatted);
    }

    public function test_it_formats_array_index_paths(): void
    {
        $errors = [
            ValidationError::make('$.items[0].name', 'Required'),
            ValidationError::make('$.items[1].name', 'Required'),
        ];

        $formatted = $this->formatter->formatForLaravel($errors);

        $this->assertArrayHasKey('items.0.name', $formatted);
        $this->assertArrayHasKey('items.1.name', $formatted);
    }
}
