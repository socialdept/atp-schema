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
     * Whether to respect the @generated marker (skip files without it).
     */
    protected bool $respectMarker = false;

    /**
     * Create a new FileWriter.
     */
    public function __construct(bool $overwrite = false, bool $createDirectories = true, bool $respectMarker = false)
    {
        $this->overwrite = $overwrite;
        $this->createDirectories = $createDirectories;
        $this->respectMarker = $respectMarker;
    }

    /**
     * Write content to a file.
     *
     * @return bool True if file was written, false if skipped (due to missing @generated marker)
     */
    public function write(string $path, string $content): bool
    {
        // Check if file exists
        if (file_exists($path)) {
            // If not allowed to overwrite at all, throw
            if (! $this->overwrite) {
                throw GenerationException::fileExists($path);
            }

            // If respecting marker, check if file is regeneratable
            if ($this->respectMarker && ! $this->isRegenerable($path)) {
                // File exists but marker was removed - skip
                return false;
            }
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

        return true;
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

    /**
     * Set whether to respect the @generated marker.
     *
     * When true, files without the @generated marker will not be overwritten.
     */
    public function setRespectMarker(bool $respectMarker): void
    {
        $this->respectMarker = $respectMarker;
    }

    /**
     * Check if a file is regenerable (has the #[Generated] attribute).
     *
     * Returns true if:
     * - File doesn't exist
     * - File has #[Generated] attribute (with regenerate: true or default)
     *
     * Returns false if:
     * - File exists but #[Generated] attribute was removed
     * - File has #[Generated(regenerate: false)]
     */
    public function isRegenerable(string $path): bool
    {
        if (! file_exists($path)) {
            return true;
        }

        $content = @file_get_contents($path);

        if ($content === false) {
            return false;
        }

        // Look for the Generated attribute in the file
        // Check for #[Generated] or #[Generated(...)]
        if (! preg_match('/#\[Generated(?:\s*\(([^)]*)\))?\s*\]/', $content, $matches)) {
            // No Generated attribute found - file was modified
            return false;
        }

        // If there are parameters, check for regenerate: false
        if (isset($matches[1]) && $matches[1] !== '') {
            $params = $matches[1];

            // Check for regenerate: false (with various spacing)
            if (preg_match('/regenerate\s*:\s*false/', $params)) {
                return false;
            }
        }

        return true;
    }
}
