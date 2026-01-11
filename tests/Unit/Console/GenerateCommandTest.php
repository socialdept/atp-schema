<?php

namespace SocialDept\AtpSchema\Tests\Unit\Console;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Console\GenerateCommand;
use SocialDept\AtpSchema\SchemaServiceProvider;

class GenerateCommandTest extends TestCase
{
    protected string $tempDir;

    protected function getPackageProviders($app): array
    {
        return [SchemaServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->tempDir = sys_get_temp_dir().'/schema-test-'.uniqid();
        mkdir($this->tempDir, 0755, true);

        config([
            'schema.sources' => [__DIR__.'/../../fixtures'],
            'schema.lexicons.output_path' => $this->tempDir,
            'schema.lexicons.base_namespace' => 'Test\\Generated',
        ]);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tempDir)) {
            $this->deleteDirectory($this->tempDir);
        }

        parent::tearDown();
    }

    protected function deleteDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $items = scandir($dir);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir.'/'.$item;

            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }

        rmdir($dir);
    }

    /**
     * Find all PHP files recursively in the temp directory.
     */
    protected function findPhpFiles(): array
    {
        if (! is_dir($this->tempDir)) {
            return [];
        }

        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->tempDir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    public function test_it_generates_code_in_dry_run_mode(): void
    {
        $this->artisan(GenerateCommand::class, [
            'nsid' => 'app.bsky.feed.post',
            '--dry-run' => true,
        ])
            ->expectsOutput('Generating DTO classes for schema: app.bsky.feed.post')
            ->expectsOutput('Dry run mode - no files will be written')
            ->assertSuccessful();
    }

    public function test_it_handles_invalid_nsid(): void
    {
        $this->artisan(GenerateCommand::class, [
            'nsid' => 'nonexistent.schema',
        ])
            ->expectsOutput('Generating DTO classes for schema: nonexistent.schema')
            ->assertFailed();
    }

    public function test_it_accepts_custom_output_directory(): void
    {
        $this->artisan(GenerateCommand::class, [
            'nsid' => 'app.bsky.feed.post',
            '--output' => '/custom/path',
            '--dry-run' => true,
        ])
            ->assertSuccessful();
    }

    public function test_it_accepts_custom_namespace(): void
    {
        $this->artisan(GenerateCommand::class, [
            'nsid' => 'app.bsky.feed.post',
            '--namespace' => 'Custom\\Namespace',
            '--dry-run' => true,
        ])
            ->assertSuccessful();
    }

    // =========================================================================
    // --force Option Tests
    // =========================================================================

    public function test_force_overwrites_file_with_generated_attribute(): void
    {
        // First generation
        $this->artisan(GenerateCommand::class, [
            'nsid' => 'app.bsky.feed.post',
            '--force' => true,
        ])->assertSuccessful();

        // Find the generated file
        $files = $this->findPhpFiles();
        $this->assertNotEmpty($files, 'Expected at least one generated file');

        $filePath = $files[0];
        $originalContent = file_get_contents($filePath);

        // Verify it has the Generated attribute
        $this->assertStringContainsString('#[Generated', $originalContent);

        // Second generation with --force should overwrite
        $this->artisan(GenerateCommand::class, [
            'nsid' => 'app.bsky.feed.post',
            '--force' => true,
        ])
            ->assertSuccessful()
            ->expectsOutputToContain('Generated');

        // File should still exist and be valid
        $this->assertFileExists($filePath);
        $newContent = file_get_contents($filePath);
        $this->assertStringContainsString('#[Generated', $newContent);
    }

    public function test_force_skips_file_with_regenerate_false(): void
    {
        // First generation
        $this->artisan(GenerateCommand::class, [
            'nsid' => 'app.bsky.feed.post',
            '--force' => true,
        ])->assertSuccessful();

        // Find the generated file
        $files = $this->findPhpFiles();
        $this->assertNotEmpty($files, 'Expected at least one generated file');

        $filePath = $files[0];
        $originalContent = file_get_contents($filePath);

        // Change regenerate: true to regenerate: false
        $modifiedContent = str_replace(
            '#[Generated(regenerate: true)]',
            '#[Generated(regenerate: false)]',
            $originalContent
        );
        file_put_contents($filePath, $modifiedContent);

        // Second generation with --force should skip this file
        $this->artisan(GenerateCommand::class, [
            'nsid' => 'app.bsky.feed.post',
            '--force' => true,
        ])
            ->assertSuccessful()
            ->expectsOutputToContain('Skipped');

        // File should still have regenerate: false
        $finalContent = file_get_contents($filePath);
        $this->assertStringContainsString('#[Generated(regenerate: false)]', $finalContent);
    }

    public function test_force_skips_file_with_no_generated_attribute(): void
    {
        // First generation
        $this->artisan(GenerateCommand::class, [
            'nsid' => 'app.bsky.feed.post',
            '--force' => true,
        ])->assertSuccessful();

        // Find the generated file
        $files = $this->findPhpFiles();
        $this->assertNotEmpty($files, 'Expected at least one generated file');

        $filePath = $files[0];
        $originalContent = file_get_contents($filePath);

        // Remove the Generated attribute entirely (user modified file)
        $modifiedContent = preg_replace('/#\[Generated\([^\)]*\)\]\n/', '', $originalContent);
        // Also remove the import
        $modifiedContent = preg_replace('/use SocialDept\\\\AtpSchema\\\\Attributes\\\\Generated;\n/', '', $modifiedContent);
        file_put_contents($filePath, $modifiedContent);

        // Verify attribute was removed
        $this->assertStringNotContainsString('#[Generated', file_get_contents($filePath));

        // Second generation with --force should skip this file
        $this->artisan(GenerateCommand::class, [
            'nsid' => 'app.bsky.feed.post',
            '--force' => true,
        ])
            ->assertSuccessful()
            ->expectsOutputToContain('Skipped');

        // File should still not have the attribute
        $finalContent = file_get_contents($filePath);
        $this->assertStringNotContainsString('#[Generated', $finalContent);
    }

    public function test_without_force_generates_new_file(): void
    {
        // Generation without --force should work for new files
        $this->artisan(GenerateCommand::class, [
            'nsid' => 'app.bsky.feed.post',
        ])
            ->assertSuccessful()
            ->expectsOutputToContain('Generated');

        // Verify file was created
        $files = $this->findPhpFiles();
        $this->assertNotEmpty($files, 'Expected at least one generated file');
    }

    public function test_without_force_fails_on_existing_file(): void
    {
        // First generation
        $this->artisan(GenerateCommand::class, [
            'nsid' => 'app.bsky.feed.post',
        ])->assertSuccessful();

        // Second generation without --force should fail
        $this->artisan(GenerateCommand::class, [
            'nsid' => 'app.bsky.feed.post',
        ])
            ->assertFailed()
            ->expectsOutputToContain('Generation failed');
    }

    public function test_force_generates_new_file(): void
    {
        // Generation with --force should work for new files
        $this->artisan(GenerateCommand::class, [
            'nsid' => 'app.bsky.feed.post',
            '--force' => true,
        ])
            ->assertSuccessful()
            ->expectsOutputToContain('Generated');

        // Verify file was created with the Generated attribute
        $files = $this->findPhpFiles();
        $this->assertNotEmpty($files, 'Expected at least one generated file');

        $content = file_get_contents($files[0]);
        $this->assertStringContainsString('#[Generated(regenerate: true)]', $content);
    }
}
