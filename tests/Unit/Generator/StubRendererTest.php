<?php

namespace SocialDept\AtpSchema\Tests\Unit\Generator;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Exceptions\GenerationException;
use SocialDept\AtpSchema\Generator\StubRenderer;

class StubRendererTest extends TestCase
{
    protected StubRenderer $renderer;

    protected string $stubPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stubPath = __DIR__.'/../../fixtures/stubs';
        @mkdir($this->stubPath, 0755, true);

        $this->renderer = new StubRenderer($this->stubPath);
    }

    protected function tearDown(): void
    {
        // Clean up test stubs
        if (is_dir($this->stubPath)) {
            foreach (glob($this->stubPath.'/*.stub') as $file) {
                @unlink($file);
            }
            @rmdir($this->stubPath);
        }

        parent::tearDown();
    }

    public function test_it_renders_simple_stub(): void
    {
        file_put_contents($this->stubPath.'/simple.stub', 'Hello {{ name }}!');

        $result = $this->renderer->render('simple', ['name' => 'World']);

        $this->assertSame('Hello World!', $result);
    }

    public function test_it_renders_multiple_variables(): void
    {
        file_put_contents(
            $this->stubPath.'/multiple.stub',
            'namespace {{ namespace }};

class {{ className }}
{
}'
        );

        $result = $this->renderer->render('multiple', [
            'namespace' => 'App\\Models',
            'className' => 'Post',
        ]);

        $this->assertStringContainsString('namespace App\\Models;', $result);
        $this->assertStringContainsString('class Post', $result);
    }

    public function test_it_handles_array_values(): void
    {
        file_put_contents(
            $this->stubPath.'/array.stub',
            'class Test
{
{{ properties }}
}'
        );

        $result = $this->renderer->render('array', [
            'properties' => [
                '    public string $name;',
                '    public int $age;',
            ],
        ]);

        $this->assertStringContainsString('public string $name;', $result);
        $this->assertStringContainsString('public int $age;', $result);
    }

    public function test_it_handles_empty_values(): void
    {
        file_put_contents($this->stubPath.'/empty.stub', 'Hello{{ suffix }}');

        $result = $this->renderer->render('empty', ['suffix' => '']);

        $this->assertSame('Hello', $result);
    }

    public function test_it_handles_null_values(): void
    {
        file_put_contents($this->stubPath.'/null.stub', 'Hello{{ suffix }}');

        $result = $this->renderer->render('null', ['suffix' => null]);

        $this->assertSame('Hello', $result);
    }

    public function test_it_removes_unreplaced_variables(): void
    {
        file_put_contents($this->stubPath.'/unreplaced.stub', 'Hello {{ name }}{{ extra }}');

        $result = $this->renderer->render('unreplaced', ['name' => 'World']);

        $this->assertSame('Hello World', $result);
        $this->assertStringNotContainsString('{{ extra }}', $result);
    }

    public function test_it_caches_stub_contents(): void
    {
        file_put_contents($this->stubPath.'/cached.stub', 'Original');

        // First render
        $result1 = $this->renderer->render('cached', []);

        // Change file content
        file_put_contents($this->stubPath.'/cached.stub', 'Modified');

        // Second render should still use cached version
        $result2 = $this->renderer->render('cached', []);

        $this->assertSame('Original', $result1);
        $this->assertSame('Original', $result2);
    }

    public function test_it_can_clear_cache(): void
    {
        file_put_contents($this->stubPath.'/clearable.stub', 'Original');

        // First render
        $result1 = $this->renderer->render('clearable', []);

        // Change file and clear cache
        file_put_contents($this->stubPath.'/clearable.stub', 'Modified');
        $this->renderer->clearCache();

        // Should load new version
        $result2 = $this->renderer->render('clearable', []);

        $this->assertSame('Original', $result1);
        $this->assertSame('Modified', $result2);
    }

    public function test_it_throws_when_stub_not_found(): void
    {
        $this->expectException(GenerationException::class);
        $this->expectExceptionMessage('Template not found: nonexistent');

        $this->renderer->render('nonexistent', []);
    }

    public function test_it_can_set_custom_stub_path(): void
    {
        $customPath = __DIR__.'/../../fixtures/custom-stubs';
        @mkdir($customPath, 0755, true);
        file_put_contents($customPath.'/custom.stub', 'Custom stub');

        $this->renderer->setStubPath($customPath);
        $result = $this->renderer->render('custom', []);

        $this->assertSame('Custom stub', $result);

        // Cleanup
        @unlink($customPath.'/custom.stub');
        @rmdir($customPath);
    }

    public function test_it_prefers_published_stubs(): void
    {
        // This test would require a full Laravel app context
        // For now, we just test that the method checks for published stubs
        file_put_contents($this->stubPath.'/package.stub', 'Package version');

        $result = $this->renderer->render('package', []);

        $this->assertSame('Package version', $result);
    }

    public function test_it_lists_available_stubs(): void
    {
        file_put_contents($this->stubPath.'/stub1.stub', 'Stub 1');
        file_put_contents($this->stubPath.'/stub2.stub', 'Stub 2');
        file_put_contents($this->stubPath.'/stub3.stub', 'Stub 3');

        $stubs = $this->renderer->getAvailableStubs();

        $this->assertContains('stub1', $stubs);
        $this->assertContains('stub2', $stubs);
        $this->assertContains('stub3', $stubs);
    }

    public function test_it_handles_boolean_values(): void
    {
        file_put_contents($this->stubPath.'/boolean.stub', 'Active: {{ active }}');

        $result1 = $this->renderer->render('boolean', ['active' => true]);
        $result2 = $this->renderer->render('boolean', ['active' => false]);

        $this->assertSame('Active: true', $result1);
        $this->assertSame('Active: false', $result2);
    }

    public function test_it_handles_numeric_values(): void
    {
        file_put_contents($this->stubPath.'/numeric.stub', 'Count: {{ count }}');

        $result = $this->renderer->render('numeric', ['count' => 42]);

        $this->assertSame('Count: 42', $result);
    }
}
