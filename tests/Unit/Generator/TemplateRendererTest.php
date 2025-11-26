<?php

namespace SocialDept\AtpSchema\Tests\Unit\Generator;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Exceptions\GenerationException;
use SocialDept\AtpSchema\Generator\TemplateRenderer;

class TemplateRendererTest extends TestCase
{
    protected TemplateRenderer $renderer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->renderer = new TemplateRenderer();
    }

    public function test_it_renders_template_with_simple_variables(): void
    {
        $template = 'Hello, {{name}}!';
        $this->renderer->registerTemplate('greeting', $template);

        $result = $this->renderer->render('greeting', ['name' => 'World']);

        $this->assertSame('Hello, World!', $result);
    }

    public function test_it_renders_record_template(): void
    {
        $result = $this->renderer->render('record', [
            'namespace' => 'App\\Lexicon\\App\\Bsky\\Feed',
            'className' => 'Post',
            'nsid' => 'app.bsky.feed.post',
            'description' => 'A post record',
            'properties' => [
                [
                    'name' => 'text',
                    'type' => 'string',
                    'phpType' => 'string',
                    'required' => true,
                    'description' => 'Post text content',
                ],
            ],
        ]);

        $this->assertStringContainsString('namespace App\\Lexicon\\App\\Bsky\\Feed', $result);
        $this->assertStringContainsString('class Post', $result);
        $this->assertStringContainsString('public readonly string $text', $result);
        $this->assertStringContainsString('Post text content', $result);
    }

    public function test_it_renders_object_template(): void
    {
        $result = $this->renderer->render('object', [
            'namespace' => 'App\\Lexicon\\App\\Bsky\\Feed',
            'className' => 'ReplyRef',
            'description' => 'A reply reference',
            'properties' => [
                [
                    'name' => 'parent',
                    'type' => 'string',
                    'phpType' => 'string',
                    'required' => true,
                    'description' => null,
                ],
            ],
        ]);

        $this->assertStringContainsString('namespace App\\Lexicon\\App\\Bsky\\Feed', $result);
        $this->assertStringContainsString('class ReplyRef', $result);
        $this->assertStringContainsString('public readonly string $parent', $result);
    }

    public function test_it_handles_nullable_properties(): void
    {
        $result = $this->renderer->render('record', [
            'namespace' => 'App\\Lexicon\\Test',
            'className' => 'Test',
            'nsid' => 'test.example.test',
            'description' => 'Test',
            'properties' => [
                [
                    'name' => 'optional',
                    'type' => 'string',
                    'phpType' => 'string',
                    'required' => false,
                    'description' => null,
                ],
            ],
        ]);

        $this->assertStringContainsString('?string $optional', $result);
    }

    public function test_it_registers_custom_template(): void
    {
        $this->renderer->registerTemplate('custom', 'Custom: {{value}}');

        $result = $this->renderer->render('custom', ['value' => 'Test']);

        $this->assertSame('Custom: Test', $result);
    }

    public function test_it_throws_on_unknown_template(): void
    {
        $this->expectException(GenerationException::class);
        $this->expectExceptionMessage('Template not found: nonexistent');

        $this->renderer->render('nonexistent', []);
    }

    public function test_it_renders_multiple_properties(): void
    {
        $result = $this->renderer->render('record', [
            'namespace' => 'App\\Lexicon\\Test',
            'className' => 'Test',
            'nsid' => 'test.example.test',
            'description' => 'Test',
            'properties' => [
                [
                    'name' => 'field1',
                    'type' => 'string',
                    'phpType' => 'string',
                    'required' => true,
                    'description' => 'First field',
                ],
                [
                    'name' => 'field2',
                    'type' => 'integer',
                    'phpType' => 'int',
                    'required' => false,
                    'description' => 'Second field',
                ],
            ],
        ]);

        $this->assertStringContainsString('public readonly string $field1', $result);
        $this->assertStringContainsString('public readonly ?int $field2', $result);
        $this->assertStringContainsString('First field', $result);
        $this->assertStringContainsString('Second field', $result);
    }
}
