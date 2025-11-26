<?php

namespace SocialDept\AtpSchema\Tests\Unit\Generator;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Exceptions\GenerationException;
use SocialDept\AtpSchema\Generator\FileWriter;

class FileWriterTest extends TestCase
{
    protected string $tempDir;

    protected FileWriter $writer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tempDir = sys_get_temp_dir().'/schema-test-'.uniqid();
        mkdir($this->tempDir, 0755, true);

        $this->writer = new FileWriter(overwrite: true, createDirectories: true);
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

    public function test_it_writes_file(): void
    {
        $path = $this->tempDir.'/test.txt';
        $content = 'Hello, World!';

        $this->writer->write($path, $content);

        $this->assertFileExists($path);
        $this->assertSame($content, file_get_contents($path));
    }

    public function test_it_creates_directories(): void
    {
        $path = $this->tempDir.'/nested/directory/test.txt';
        $content = 'Test content';

        $this->writer->write($path, $content);

        $this->assertFileExists($path);
        $this->assertDirectoryExists(dirname($path));
    }

    public function test_it_overwrites_existing_file_when_enabled(): void
    {
        $path = $this->tempDir.'/test.txt';

        $this->writer->write($path, 'Original');
        $this->writer->write($path, 'Updated');

        $this->assertSame('Updated', file_get_contents($path));
    }

    public function test_it_throws_when_file_exists_and_overwrite_disabled(): void
    {
        $writer = new FileWriter(overwrite: false);
        $path = $this->tempDir.'/test.txt';

        file_put_contents($path, 'Existing content');

        $this->expectException(GenerationException::class);
        $this->expectExceptionMessage('File already exists');

        $writer->write($path, 'New content');
    }

    public function test_it_checks_if_file_exists(): void
    {
        $path = $this->tempDir.'/test.txt';

        $this->assertFalse($this->writer->exists($path));

        file_put_contents($path, 'Content');

        $this->assertTrue($this->writer->exists($path));
    }

    public function test_it_deletes_file(): void
    {
        $path = $this->tempDir.'/test.txt';

        file_put_contents($path, 'Content');
        $this->assertFileExists($path);

        $this->writer->delete($path);

        $this->assertFileDoesNotExist($path);
    }

    public function test_it_reads_file(): void
    {
        $path = $this->tempDir.'/test.txt';
        $content = 'Test content';

        file_put_contents($path, $content);

        $this->assertSame($content, $this->writer->read($path));
    }

    public function test_it_throws_when_reading_nonexistent_file(): void
    {
        $path = $this->tempDir.'/nonexistent.txt';

        $this->expectException(GenerationException::class);
        $this->expectExceptionMessage('File not found');

        $this->writer->read($path);
    }

    public function test_it_sets_overwrite_option(): void
    {
        $writer = new FileWriter(overwrite: false);
        $path = $this->tempDir.'/test.txt';

        file_put_contents($path, 'Existing');

        $writer->setOverwrite(true);
        $writer->write($path, 'Updated');

        $this->assertSame('Updated', file_get_contents($path));
    }
}
