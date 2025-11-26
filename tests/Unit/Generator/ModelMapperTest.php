<?php

namespace SocialDept\AtpSchema\Tests\Unit\Generator;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Generator\ModelMapper;

class ModelMapperTest extends TestCase
{
    protected ModelMapper $mapper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mapper = new ModelMapper();
    }

    public function test_it_generates_to_model_body_for_simple_properties(): void
    {
        $body = $this->mapper->generateToModelBody([
            'name' => ['type' => 'string'],
            'age' => ['type' => 'integer'],
        ], 'User');

        $this->assertStringContainsString('return new User([', $body);
        $this->assertStringContainsString("'name' => \$this->name,", $body);
        $this->assertStringContainsString("'age' => \$this->age,", $body);
    }

    public function test_it_generates_from_model_body_for_simple_properties(): void
    {
        $body = $this->mapper->generateFromModelBody([
            'name' => ['type' => 'string'],
            'age' => ['type' => 'integer'],
        ]);

        $this->assertStringContainsString('return new static(', $body);
        $this->assertStringContainsString('name: $model->name ?? null,', $body);
        $this->assertStringContainsString('age: $model->age ?? null', $body);
    }

    public function test_it_handles_datetime_in_to_model(): void
    {
        $body = $this->mapper->generateToModelBody([
            'createdAt' => [
                'type' => 'string',
                'format' => 'datetime',
            ],
        ]);

        $this->assertStringContainsString("\$this->createdAt?->format('Y-m-d H:i:s')", $body);
    }

    public function test_it_handles_datetime_in_from_model(): void
    {
        $body = $this->mapper->generateFromModelBody([
            'createdAt' => [
                'type' => 'string',
                'format' => 'datetime',
            ],
        ]);

        $this->assertStringContainsString('$model->createdAt ? new \\DateTime($model->createdAt) : null', $body);
    }

    public function test_it_handles_blob_in_to_model(): void
    {
        $body = $this->mapper->generateToModelBody([
            'image' => ['type' => 'blob'],
        ]);

        $this->assertStringContainsString('$this->image?->toArray()', $body);
    }

    public function test_it_handles_blob_in_from_model(): void
    {
        $body = $this->mapper->generateFromModelBody([
            'image' => ['type' => 'blob'],
        ]);

        $this->assertStringContainsString('\\SocialDept\\AtpSchema\\Data\\BlobReference::fromArray', $body);
    }

    public function test_it_handles_ref_in_to_model(): void
    {
        $body = $this->mapper->generateToModelBody([
            'author' => [
                'type' => 'ref',
                'ref' => 'app.test.author',
            ],
        ]);

        $this->assertStringContainsString('$this->author?->toArray()', $body);
    }

    public function test_it_handles_ref_in_from_model(): void
    {
        $body = $this->mapper->generateFromModelBody([
            'author' => [
                'type' => 'ref',
                'ref' => 'app.test.author',
            ],
        ]);

        $this->assertStringContainsString('Author::fromArray', $body);
    }

    public function test_it_handles_array_of_refs_in_to_model(): void
    {
        $body = $this->mapper->generateToModelBody([
            'posts' => [
                'type' => 'array',
                'items' => [
                    'type' => 'ref',
                    'ref' => 'app.test.post',
                ],
            ],
        ]);

        $this->assertStringContainsString('array_map(fn ($item) => $item->toArray()', $body);
    }

    public function test_it_handles_array_of_refs_in_from_model(): void
    {
        $body = $this->mapper->generateFromModelBody([
            'posts' => [
                'type' => 'array',
                'items' => [
                    'type' => 'ref',
                    'ref' => 'app.test.post',
                ],
            ],
        ]);

        $this->assertStringContainsString('array_map(fn ($item) => Post::fromArray($item)', $body);
    }

    public function test_it_handles_array_of_objects(): void
    {
        $body = $this->mapper->generateToModelBody([
            'settings' => [
                'type' => 'array',
                'items' => [
                    'type' => 'object',
                ],
            ],
        ]);

        $this->assertStringContainsString('$this->settings ?? []', $body);
    }

    public function test_it_generates_empty_to_model_for_no_properties(): void
    {
        $body = $this->mapper->generateToModelBody([], 'User');

        $this->assertStringContainsString('return new User();', $body);
    }

    public function test_it_generates_empty_from_model_for_no_properties(): void
    {
        $body = $this->mapper->generateFromModelBody([]);

        $this->assertStringContainsString('return new static();', $body);
    }

    public function test_it_gets_field_mapping(): void
    {
        $mapping = $this->mapper->getFieldMapping([
            'userName' => ['type' => 'string'],
            'emailAddress' => ['type' => 'string'],
        ]);

        $this->assertSame([
            'userName' => 'user_name',
            'emailAddress' => 'email_address',
        ], $mapping);
    }

    public function test_it_checks_if_datetime_needs_transformer(): void
    {
        $this->assertTrue($this->mapper->needsTransformer([
            'type' => 'string',
            'format' => 'datetime',
        ]));
    }

    public function test_it_checks_if_blob_needs_transformer(): void
    {
        $this->assertTrue($this->mapper->needsTransformer(['type' => 'blob']));
    }

    public function test_it_checks_if_ref_needs_transformer(): void
    {
        $this->assertTrue($this->mapper->needsTransformer(['type' => 'ref']));
    }

    public function test_it_checks_if_array_of_refs_needs_transformer(): void
    {
        $this->assertTrue($this->mapper->needsTransformer([
            'type' => 'array',
            'items' => ['type' => 'ref'],
        ]));
    }

    public function test_it_checks_if_simple_type_needs_transformer(): void
    {
        $this->assertFalse($this->mapper->needsTransformer(['type' => 'string']));
        $this->assertFalse($this->mapper->needsTransformer(['type' => 'integer']));
    }

    public function test_it_gets_datetime_transformer_type(): void
    {
        $type = $this->mapper->getTransformerType([
            'type' => 'string',
            'format' => 'datetime',
        ]);

        $this->assertSame('datetime', $type);
    }

    public function test_it_gets_blob_transformer_type(): void
    {
        $type = $this->mapper->getTransformerType(['type' => 'blob']);

        $this->assertSame('blob', $type);
    }

    public function test_it_gets_ref_transformer_type(): void
    {
        $type = $this->mapper->getTransformerType(['type' => 'ref']);

        $this->assertSame('ref', $type);
    }

    public function test_it_gets_array_ref_transformer_type(): void
    {
        $type = $this->mapper->getTransformerType([
            'type' => 'array',
            'items' => ['type' => 'ref'],
        ]);

        $this->assertSame('array_ref', $type);
    }

    public function test_it_gets_array_object_transformer_type(): void
    {
        $type = $this->mapper->getTransformerType([
            'type' => 'array',
            'items' => ['type' => 'object'],
        ]);

        $this->assertSame('array_object', $type);
    }

    public function test_it_gets_null_transformer_for_simple_types(): void
    {
        $this->assertNull($this->mapper->getTransformerType(['type' => 'string']));
        $this->assertNull($this->mapper->getTransformerType(['type' => 'integer']));
    }

    public function test_it_handles_custom_model_class(): void
    {
        $body = $this->mapper->generateToModelBody([
            'name' => ['type' => 'string'],
        ], 'CustomModel');

        $this->assertStringContainsString('return new CustomModel([', $body);
    }

    public function test_it_does_not_add_trailing_comma_to_last_property(): void
    {
        $body = $this->mapper->generateFromModelBody([
            'first' => ['type' => 'string'],
            'last' => ['type' => 'string'],
        ]);

        // Should not have comma after last property
        $lines = explode("\n", $body);
        $lastPropertyLine = '';
        foreach ($lines as $line) {
            if (str_contains($line, 'last:')) {
                $lastPropertyLine = $line;

                break;
            }
        }

        $this->assertStringNotContainsString(',', rtrim($lastPropertyLine));
    }
}
