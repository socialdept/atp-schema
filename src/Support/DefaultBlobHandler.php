<?php

namespace SocialDept\Schema\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use SocialDept\Schema\Contracts\BlobHandler;
use SocialDept\Schema\Data\BlobReference;

class DefaultBlobHandler implements BlobHandler
{
    /**
     * Storage disk to use.
     */
    protected string $disk;

    /**
     * Storage path prefix.
     */
    protected string $path;

    /**
     * Create a new DefaultBlobHandler.
     */
    public function __construct(
        ?string $disk = null,
        string $path = 'blobs'
    ) {
        $this->disk = $disk ?? config('filesystems.default', 'local');
        $this->path = $path;
    }

    /**
     * Upload blob and create reference.
     */
    public function upload(UploadedFile $file): BlobReference
    {
        $hash = hash_file('sha256', $file->getPathname());
        $mimeType = $file->getMimeType() ?? 'application/octet-stream';
        $size = $file->getSize();

        // Store with hash as filename to enable deduplication
        $storagePath = $this->path.'/'.$hash;
        Storage::disk($this->disk)->put($storagePath, file_get_contents($file->getPathname()));

        return new BlobReference(
            cid: $hash, // Using hash as CID for simplicity
            mimeType: $mimeType,
            size: $size
        );
    }

    /**
     * Upload blob from path.
     */
    public function uploadFromPath(string $path): BlobReference
    {
        $hash = hash_file('sha256', $path);
        $mimeType = mime_content_type($path) ?: 'application/octet-stream';
        $size = filesize($path);

        // Store with hash as filename
        $storagePath = $this->path.'/'.$hash;
        Storage::disk($this->disk)->put($storagePath, file_get_contents($path));

        return new BlobReference(
            cid: $hash,
            mimeType: $mimeType,
            size: $size
        );
    }

    /**
     * Upload blob from content.
     */
    public function uploadFromContent(string $content, string $mimeType): BlobReference
    {
        $hash = hash('sha256', $content);
        $size = strlen($content);

        // Store with hash as filename
        $storagePath = $this->path.'/'.$hash;
        Storage::disk($this->disk)->put($storagePath, $content);

        return new BlobReference(
            cid: $hash,
            mimeType: $mimeType,
            size: $size
        );
    }

    /**
     * Download blob content.
     */
    public function download(BlobReference $blob): string
    {
        $storagePath = $this->path.'/'.$blob->cid;

        if (! Storage::disk($this->disk)->exists($storagePath)) {
            throw new \RuntimeException("Blob not found: {$blob->cid}");
        }

        return Storage::disk($this->disk)->get($storagePath);
    }

    /**
     * Generate signed URL for blob.
     */
    public function url(BlobReference $blob): string
    {
        $storagePath = $this->path.'/'.$blob->cid;

        // Try to generate a temporary URL if the disk supports it
        try {
            return Storage::disk($this->disk)->temporaryUrl(
                $storagePath,
                now()->addHour()
            );
        } catch (\RuntimeException $e) {
            // Fallback to regular URL for disks that don't support temporary URLs
            return Storage::disk($this->disk)->url($storagePath);
        }
    }

    /**
     * Check if blob exists in storage.
     */
    public function exists(BlobReference $blob): bool
    {
        $storagePath = $this->path.'/'.$blob->cid;

        return Storage::disk($this->disk)->exists($storagePath);
    }

    /**
     * Delete blob from storage.
     */
    public function delete(BlobReference $blob): bool
    {
        $storagePath = $this->path.'/'.$blob->cid;

        if (! Storage::disk($this->disk)->exists($storagePath)) {
            return false;
        }

        return Storage::disk($this->disk)->delete($storagePath);
    }
}
