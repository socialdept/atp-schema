<?php

namespace SocialDept\Schema\Tests\Unit\Generator;

use Orchestra\Testbench\TestCase;
use SocialDept\Schema\Generator\ConstructorGenerator;

class ConstructorGeneratorTest extends TestCase
{
    protected ConstructorGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = new ConstructorGenerator();
    }

    public function test_it_generates_constructor_with_promoted_properties(): void
    {
        $constructor = $this->generator->generate(
            [
                'name' => ['type' => 'string'],
                'age' => ['type' => 'integer'],
            ],
            ['name', 'age']
        );

        $this->assertStringContainsString('public function __construct(', $constructor);
        $this->assertStringContainsString('public readonly string $name', $constructor);
        $this->assertStringContainsString('public readonly int $age', $constructor);
    }

    public function test_it_handles_optional_parameters(): void
    {
        $constructor = $this->generator->generate(
            [
                'name' => ['type' => 'string'],
                'nickname' => ['type' => 'string'],
            ],
            ['name']
        );

        $this->assertStringContainsString('public readonly string $name', $constructor);
        $this->assertStringContainsString('public readonly ?string $nickname', $constructor);
    }

    public function test_it_returns_empty_for_no_properties(): void
    {
        $constructor = $this->generator->generate([], []);

        $this->assertEmpty($constructor);
    }

    public function test_it_generates_docblock(): void
    {
        $constructor = $this->generator->generate(
            [
                'name' => ['type' => 'string'],
            ],
            ['name']
        );

        $this->assertStringContainsString('/**', $constructor);
        $this->assertStringContainsString('* Create a new instance.', $constructor);
        $this->assertStringContainsString('* @param  string  $name', $constructor);
    }

    public function test_it_includes_descriptions_in_docblock(): void
    {
        $constructor = $this->generator->generate(
            [
                'name' => [
                    'type' => 'string',
                    'description' => 'The user name',
                ],
            ],
            ['name']
        );

        $this->assertStringContainsString('The user name', $constructor);
    }

    public function test_it_handles_multiple_properties(): void
    {
        $constructor = $this->generator->generate(
            [
                'id' => ['type' => 'string'],
                'name' => ['type' => 'string'],
                'email' => ['type' => 'string'],
                'age' => ['type' => 'integer'],
                'active' => ['type' => 'boolean'],
            ],
            ['id', 'name', 'email']
        );

        $this->assertStringContainsString('$id', $constructor);
        $this->assertStringContainsString('$name', $constructor);
        $this->assertStringContainsString('$email', $constructor);
        $this->assertStringContainsString('$age', $constructor);
        $this->assertStringContainsString('$active', $constructor);
    }

    public function test_it_generates_with_assignments(): void
    {
        $constructor = $this->generator->generateWithAssignments(
            [
                'name' => ['type' => 'string'],
                'age' => ['type' => 'integer'],
            ],
            ['name', 'age']
        );

        $this->assertStringContainsString('public function __construct(', $constructor);
        $this->assertStringContainsString('string $name,', $constructor);
        $this->assertStringContainsString('int $age', $constructor);
        $this->assertStringContainsString('$this->name = $name;', $constructor);
        $this->assertStringContainsString('$this->age = $age;', $constructor);
    }

    public function test_it_checks_if_constructor_should_be_generated(): void
    {
        $this->assertTrue($this->generator->shouldGenerate(['name' => ['type' => 'string']]));
        $this->assertFalse($this->generator->shouldGenerate([]));
    }

    public function test_it_handles_default_values(): void
    {
        $constructor = $this->generator->generate(
            [
                'status' => [
                    'type' => 'string',
                    'default' => 'active',
                ],
            ],
            []
        );

        $this->assertStringContainsString("= 'active'", $constructor);
    }

    public function test_it_handles_nullable_with_null_default(): void
    {
        $constructor = $this->generator->generate(
            [
                'middleName' => [
                    'type' => 'string',
                    'default' => null,
                ],
            ],
            []
        );

        $this->assertStringContainsString('?string $middleName = null', $constructor);
    }

    public function test_it_handles_all_primitive_types(): void
    {
        $constructor = $this->generator->generate(
            [
                'name' => ['type' => 'string'],
                'age' => ['type' => 'integer'],
                'price' => ['type' => 'number'],
                'active' => ['type' => 'boolean'],
                'tags' => ['type' => 'array'],
            ],
            ['name', 'age', 'price', 'active', 'tags']
        );

        $this->assertStringContainsString('string $name', $constructor);
        $this->assertStringContainsString('int $age', $constructor);
        $this->assertStringContainsString('float $price', $constructor);
        $this->assertStringContainsString('bool $active', $constructor);
        $this->assertStringContainsString('array $tags', $constructor);
    }

    public function test_it_does_not_add_trailing_comma_to_last_parameter(): void
    {
        $constructor = $this->generator->generate(
            [
                'first' => ['type' => 'string'],
                'last' => ['type' => 'string'],
            ],
            ['first', 'last']
        );

        // Last parameter should not have a trailing comma
        $this->assertStringNotContainsString('$last,', $constructor);
        $this->assertStringContainsString('$last', $constructor);
    }

    public function test_it_formats_parameters_with_proper_indentation(): void
    {
        $constructor = $this->generator->generate(
            [
                'name' => ['type' => 'string'],
            ],
            ['name']
        );

        // Check for proper indentation (8 spaces for parameters)
        $this->assertStringContainsString('        public readonly string $name', $constructor);
    }

    public function test_it_generates_empty_body_for_promoted_properties(): void
    {
        $constructor = $this->generator->generate(
            [
                'name' => ['type' => 'string'],
            ],
            ['name']
        );

        // Constructor body should be empty for promoted properties
        $this->assertMatchesRegularExpression('/\) \{\s+\}/', $constructor);
    }

    public function test_it_handles_blob_type(): void
    {
        $constructor = $this->generator->generate(
            [
                'image' => ['type' => 'blob'],
            ],
            []
        );

        $this->assertStringContainsString('BlobReference', $constructor);
    }

    public function test_it_handles_ref_type(): void
    {
        $constructor = $this->generator->generate(
            [
                'author' => [
                    'type' => 'ref',
                    'ref' => 'app.test.author',
                ],
            ],
            ['author']
        );

        $this->assertStringContainsString('App\\Lexicon\\Test\\App\\Author', $constructor);
    }
}
