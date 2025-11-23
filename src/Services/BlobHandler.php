<?php

namespace SocialDept\Schema\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Traits\Macroable;
use SocialDept\Schema\Data\BlobReference;
use SocialDept\Schema\Exceptions\RecordValidationException;

class BlobHandler
{
    use Macroable;

    /**
     * Storage disk name.
     */
    protected string $disk;

    /**
     * Base path for blob storage.
     */
    protected string $basePath;

    /**
     * Create a new BlobHandler.
     */
    public function __construct(?string $disk = null, string $basePath = 'blobs')
    {
        $this->disk = $disk ?? config('filesystems.default', 'local');
        $this->basePath = $basePath;
    }

    /**
     * Store a blob from an uploaded file.
     */
    public function store(UploadedFile $file, ?array $constraints = null): BlobReference
    {
        // Validate constraints if provided
        if ($constraints !== null) {
            $this->validateConstraints($file, $constraints);
        }

        // Generate CID-like identifier (in production, this would be actual CID)
        $cid = $this->generateCid($file);

        // Store the file
        $path = $this->getPath($cid);
        $this->getStorage()->put($path, $file->get());

        return new BlobReference(
            ref: $cid,
            mimeType: $file->getMimeType() ?? 'application/octet-stream',
            size: $file->getSize()
        );
    }

    /**
     * Store a blob from string content.
     */
    public function storeFromString(string $content, string $mimeType, ?array $constraints = null): BlobReference
    {
        $size = strlen($content);

        // Validate constraints if provided
        if ($constraints !== null) {
            $this->validateStringConstraints($content, $mimeType, $size, $constraints);
        }

        // Generate CID
        $cid = $this->generateCidFromContent($content);

        // Store the content
        $path = $this->getPath($cid);
        $this->getStorage()->put($path, $content);

        return new BlobReference(
            ref: $cid,
            mimeType: $mimeType,
            size: $size
        );
    }

    /**
     * Retrieve blob content.
     */
    public function get(string $cid): ?string
    {
        $path = $this->getPath($cid);

        if (! $this->getStorage()->exists($path)) {
            return null;
        }

        return $this->getStorage()->get($path);
    }

    /**
     * Check if blob exists.
     */
    public function exists(string $cid): bool
    {
        return $this->getStorage()->exists($this->getPath($cid));
    }

    /**
     * Delete a blob.
     */
    public function delete(string $cid): bool
    {
        $path = $this->getPath($cid);

        if (! $this->getStorage()->exists($path)) {
            return false;
        }

        return $this->getStorage()->delete($path);
    }

    /**
     * Get blob size.
     */
    public function size(string $cid): ?int
    {
        $path = $this->getPath($cid);

        if (! $this->getStorage()->exists($path)) {
            return null;
        }

        return $this->getStorage()->size($path);
    }

    /**
     * Validate blob against constraints.
     */
    public function validate(BlobReference $blob, array $constraints): void
    {
        // Validate MIME type
        if (isset($constraints['accept'])) {
            $accepted = (array) $constraints['accept'];
            $matches = false;

            foreach ($accepted as $pattern) {
                if ($blob->matchesMimeType($pattern)) {
                    $matches = true;

                    break;
                }
            }

            if (! $matches) {
                throw RecordValidationException::invalidValue(
                    'blob',
                    "MIME type '{$blob->mimeType}' not accepted. Allowed: ".implode(', ', $accepted)
                );
            }
        }

        // Validate size constraints
        if (isset($constraints['maxSize']) && $blob->size > $constraints['maxSize']) {
            throw RecordValidationException::invalidValue(
                'blob',
                "Blob size {$blob->size} exceeds maximum {$constraints['maxSize']}"
            );
        }

        if (isset($constraints['minSize']) && $blob->size < $constraints['minSize']) {
            throw RecordValidationException::invalidValue(
                'blob',
                "Blob size {$blob->size} is less than minimum {$constraints['minSize']}"
            );
        }
    }

    /**
     * Validate file against constraints.
     */
    protected function validateConstraints(UploadedFile $file, array $constraints): void
    {
        $mimeType = $file->getMimeType() ?? 'application/octet-stream';
        $size = $file->getSize();

        // Validate MIME type
        if (isset($constraints['accept'])) {
            $accepted = (array) $constraints['accept'];
            $matches = false;

            foreach ($accepted as $pattern) {
                if ($this->matchesMimeType($mimeType, $pattern)) {
                    $matches = true;

                    break;
                }
            }

            if (! $matches) {
                throw RecordValidationException::invalidValue(
                    'file',
                    "MIME type '{$mimeType}' not accepted. Allowed: ".implode(', ', $accepted)
                );
            }
        }

        // Validate size
        if (isset($constraints['maxSize']) && $size > $constraints['maxSize']) {
            throw RecordValidationException::invalidValue(
                'file',
                "File size {$size} exceeds maximum {$constraints['maxSize']}"
            );
        }

        if (isset($constraints['minSize']) && $size < $constraints['minSize']) {
            throw RecordValidationException::invalidValue(
                'file',
                "File size {$size} is less than minimum {$constraints['minSize']}"
            );
        }
    }

    /**
     * Validate string content against constraints.
     */
    protected function validateStringConstraints(string $content, string $mimeType, int $size, array $constraints): void
    {
        // Validate MIME type
        if (isset($constraints['accept'])) {
            $accepted = (array) $constraints['accept'];
            $matches = false;

            foreach ($accepted as $pattern) {
                if ($this->matchesMimeType($mimeType, $pattern)) {
                    $matches = true;

                    break;
                }
            }

            if (! $matches) {
                throw RecordValidationException::invalidValue(
                    'content',
                    "MIME type '{$mimeType}' not accepted. Allowed: ".implode(', ', $accepted)
                );
            }
        }

        // Validate size
        if (isset($constraints['maxSize']) && $size > $constraints['maxSize']) {
            throw RecordValidationException::invalidValue(
                'content',
                "Content size {$size} exceeds maximum {$constraints['maxSize']}"
            );
        }

        if (isset($constraints['minSize']) && $size < $constraints['minSize']) {
            throw RecordValidationException::invalidValue(
                'content',
                "Content size {$size} is less than minimum {$constraints['minSize']}"
            );
        }
    }

    /**
     * Check if MIME type matches pattern.
     */
    protected function matchesMimeType(string $mimeType, string $pattern): bool
    {
        if (str_contains($pattern, '*')) {
            $regex = '/^'.str_replace('\\*', '.*', preg_quote($pattern, '/')).'$/';

            return (bool) preg_match($regex, $mimeType);
        }

        return $mimeType === $pattern;
    }

    /**
     * Generate a CID-like identifier from file.
     */
    protected function generateCid(UploadedFile $file): string
    {
        // In production, this would generate an actual CID
        // For now, use a hash-based approach
        $hash = hash('sha256', $file->get());

        return 'bafyrei'.substr($hash, 0, 52);
    }

    /**
     * Generate a CID-like identifier from content.
     */
    protected function generateCidFromContent(string $content): string
    {
        $hash = hash('sha256', $content);

        return 'bafyrei'.substr($hash, 0, 52);
    }

    /**
     * Get storage path for CID.
     */
    protected function getPath(string $cid): string
    {
        // Use first 2 chars for directory partitioning
        $prefix = substr($cid, 0, 2);
        $middle = substr($cid, 2, 2);

        return "{$this->basePath}/{$prefix}/{$middle}/{$cid}";
    }

    /**
     * Get the storage instance.
     */
    protected function getStorage(): Filesystem
    {
        return Storage::disk($this->disk);
    }

    /**
     * Set the storage disk.
     */
    public function setDisk(string $disk): self
    {
        $this->disk = $disk;

        return $this;
    }

    /**
     * Set the base path.
     */
    public function setBasePath(string $basePath): self
    {
        $this->basePath = $basePath;

        return $this;
    }

    /**
     * Get the current disk name.
     */
    public function getDisk(): string
    {
        return $this->disk;
    }

    /**
     * Get the current base path.
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }
}
