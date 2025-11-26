<?php

namespace SocialDept\AtpSchema\Contracts;

use Illuminate\Http\UploadedFile;
use SocialDept\AtpSchema\Data\BlobReference;

interface BlobHandler
{
    /**
     * Upload blob and create reference.
     */
    public function upload(UploadedFile $file): BlobReference;

    /**
     * Upload blob from path.
     */
    public function uploadFromPath(string $path): BlobReference;

    /**
     * Upload blob from content.
     */
    public function uploadFromContent(string $content, string $mimeType): BlobReference;

    /**
     * Download blob content.
     */
    public function download(BlobReference $blob): string;

    /**
     * Generate signed URL for blob.
     */
    public function url(BlobReference $blob): string;

    /**
     * Check if blob exists in storage.
     */
    public function exists(BlobReference $blob): bool;

    /**
     * Delete blob from storage.
     */
    public function delete(BlobReference $blob): bool;
}
