<?php

namespace SocialDept\AtpSchema\Generator;

use SocialDept\AtpSchema\Exceptions\GenerationException;

class FileWriter
{
    /**
     * Whether to overwrite existing files.
     */
    protected bool $overwrite = false;

    /**
     * Whether to create directories if they don't exist.
     */
    protected bool $createDirectories = true;

    /**
     * Create a new FileWriter.
     */
    public function __construct(bool $overwrite = false, bool $createDirectories = true)
    {
        $this->overwrite = $overwrite;
        $this->createDirectories = $createDirectories;
    }

    /**
     * Write content to a file.
     */
    public function write(string $path, string $content): void
    {
        // Check if file exists and we're not allowed to overwrite
        if (file_exists($path) && ! $this->overwrite) {
            throw GenerationException::fileExists($path);
        }

        // Create directory if it doesn't exist
        $directory = dirname($path);

        if (! is_dir($directory)) {
            if (! $this->createDirectories) {
                throw GenerationException::directoryNotFound($directory);
            }

            if (! @mkdir($directory, 0755, true) && ! is_dir($directory)) {
                throw GenerationException::cannotCreateDirectory($directory);
            }
        }

        // Write file
        $result = @file_put_contents($path, $content);

        if ($result === false) {
            throw GenerationException::cannotWriteFile($path);
        }
    }

    /**
     * Check if file exists.
     */
    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * Delete a file.
     */
    public function delete(string $path): void
    {
        if (! file_exists($path)) {
            return;
        }

        if (! @unlink($path)) {
            throw GenerationException::cannotDeleteFile($path);
        }
    }

    /**
     * Read file content.
     */
    public function read(string $path): string
    {
        if (! file_exists($path)) {
            throw GenerationException::fileNotFound($path);
        }

        $content = @file_get_contents($path);

        if ($content === false) {
            throw GenerationException::cannotReadFile($path);
        }

        return $content;
    }

    /**
     * Set whether to overwrite existing files.
     */
    public function setOverwrite(bool $overwrite): void
    {
        $this->overwrite = $overwrite;
    }

    /**
     * Set whether to create directories.
     */
    public function setCreateDirectories(bool $createDirectories): void
    {
        $this->createDirectories = $createDirectories;
    }
}
