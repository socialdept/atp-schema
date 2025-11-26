<?php

namespace SocialDept\AtpSchema\Tests\Unit\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Data\BlobReference;
use SocialDept\AtpSchema\Exceptions\RecordValidationException;
use SocialDept\AtpSchema\Services\BlobHandler;

class BlobHandlerTest extends TestCase
{
    protected BlobHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        $this->handler = new BlobHandler('local');
    }

    public function test_it_stores_uploaded_file(): void
    {
        $file = UploadedFile::fake()->image('photo.jpg', 100, 100);

        $blob = $this->handler->store($file);

        $this->assertInstanceOf(BlobReference::class, $blob);
        $this->assertStringStartsWith('bafyrei', $blob->ref);
        $this->assertEquals('image/jpeg', $blob->mimeType);
        $this->assertGreaterThan(0, $blob->size);
        $this->assertTrue($this->handler->exists($blob->ref));
    }

    public function test_it_stores_file_with_valid_constraints(): void
    {
        $file = UploadedFile::fake()->image('photo.jpg', 100, 100);

        $constraints = [
            'accept' => ['image/*'],
            'maxSize' => 1024 * 1024 * 10, // 10MB
        ];

        $blob = $this->handler->store($file, $constraints);

        $this->assertInstanceOf(BlobReference::class, $blob);
        $this->assertTrue($this->handler->exists($blob->ref));
    }

    public function test_it_rejects_file_with_invalid_mime_type(): void
    {
        $this->expectException(RecordValidationException::class);

        $file = UploadedFile::fake()->image('photo.jpg');

        $constraints = [
            'accept' => ['video/*'],
        ];

        $this->handler->store($file, $constraints);
    }

    public function test_it_rejects_file_exceeding_max_size(): void
    {
        $this->expectException(RecordValidationException::class);

        $file = UploadedFile::fake()->create('large.pdf', 2000); // 2MB

        $constraints = [
            'maxSize' => 1024, // 1KB
        ];

        $this->handler->store($file, $constraints);
    }

    public function test_it_rejects_file_below_min_size(): void
    {
        $this->expectException(RecordValidationException::class);

        $file = UploadedFile::fake()->create('small.txt', 1);

        $constraints = [
            'minSize' => 1024 * 1024, // 1MB
        ];

        $this->handler->store($file, $constraints);
    }

    public function test_it_stores_from_string(): void
    {
        $content = 'Hello, world!';

        $blob = $this->handler->storeFromString($content, 'text/plain');

        $this->assertInstanceOf(BlobReference::class, $blob);
        $this->assertEquals('text/plain', $blob->mimeType);
        $this->assertEquals(strlen($content), $blob->size);
        $this->assertTrue($this->handler->exists($blob->ref));
    }

    public function test_it_stores_string_with_valid_constraints(): void
    {
        $content = 'Test content';

        $constraints = [
            'accept' => ['text/*'],
            'maxSize' => 1024,
        ];

        $blob = $this->handler->storeFromString($content, 'text/plain', $constraints);

        $this->assertInstanceOf(BlobReference::class, $blob);
    }

    public function test_it_rejects_string_with_invalid_mime_type(): void
    {
        $this->expectException(RecordValidationException::class);

        $constraints = [
            'accept' => ['image/*'],
        ];

        $this->handler->storeFromString('content', 'text/plain', $constraints);
    }

    public function test_it_retrieves_blob_content(): void
    {
        $content = 'Test content';
        $blob = $this->handler->storeFromString($content, 'text/plain');

        $retrieved = $this->handler->get($blob->ref);

        $this->assertEquals($content, $retrieved);
    }

    public function test_it_returns_null_for_nonexistent_blob(): void
    {
        $content = $this->handler->get('nonexistent-cid');

        $this->assertNull($content);
    }

    public function test_it_checks_blob_existence(): void
    {
        $blob = $this->handler->storeFromString('content', 'text/plain');

        $this->assertTrue($this->handler->exists($blob->ref));
        $this->assertFalse($this->handler->exists('nonexistent-cid'));
    }

    public function test_it_deletes_blob(): void
    {
        $blob = $this->handler->storeFromString('content', 'text/plain');

        $this->assertTrue($this->handler->exists($blob->ref));

        $result = $this->handler->delete($blob->ref);

        $this->assertTrue($result);
        $this->assertFalse($this->handler->exists($blob->ref));
    }

    public function test_it_returns_false_when_deleting_nonexistent_blob(): void
    {
        $result = $this->handler->delete('nonexistent-cid');

        $this->assertFalse($result);
    }

    public function test_it_gets_blob_size(): void
    {
        $content = 'Test content';
        $blob = $this->handler->storeFromString($content, 'text/plain');

        $size = $this->handler->size($blob->ref);

        $this->assertEquals(strlen($content), $size);
    }

    public function test_it_returns_null_size_for_nonexistent_blob(): void
    {
        $size = $this->handler->size('nonexistent-cid');

        $this->assertNull($size);
    }

    public function test_it_validates_blob_reference(): void
    {
        $blob = new BlobReference('cid', 'image/png', 1024);

        $constraints = [
            'accept' => ['image/*'],
            'maxSize' => 2048,
        ];

        $this->handler->validate($blob, $constraints);

        $this->assertTrue(true); // No exception thrown
    }

    public function test_it_rejects_blob_with_invalid_mime_type(): void
    {
        $this->expectException(RecordValidationException::class);

        $blob = new BlobReference('cid', 'text/plain', 1024);

        $constraints = [
            'accept' => ['image/*'],
        ];

        $this->handler->validate($blob, $constraints);
    }

    public function test_it_rejects_blob_exceeding_max_size(): void
    {
        $this->expectException(RecordValidationException::class);

        $blob = new BlobReference('cid', 'image/png', 2048);

        $constraints = [
            'maxSize' => 1024,
        ];

        $this->handler->validate($blob, $constraints);
    }

    public function test_it_rejects_blob_below_min_size(): void
    {
        $this->expectException(RecordValidationException::class);

        $blob = new BlobReference('cid', 'image/png', 512);

        $constraints = [
            'minSize' => 1024,
        ];

        $this->handler->validate($blob, $constraints);
    }

    public function test_it_validates_multiple_mime_types(): void
    {
        $blob = new BlobReference('cid', 'image/png', 1024);

        $constraints = [
            'accept' => ['image/jpeg', 'image/png', 'image/webp'],
        ];

        $this->handler->validate($blob, $constraints);

        $this->assertTrue(true);
    }

    public function test_it_validates_wildcard_mime_types(): void
    {
        $imageBlob = new BlobReference('cid1', 'image/png', 1024);
        $videoBlob = new BlobReference('cid2', 'video/mp4', 1024);

        $imageConstraints = ['accept' => ['image/*']];
        $videoConstraints = ['accept' => ['video/*']];

        $this->handler->validate($imageBlob, $imageConstraints);
        $this->handler->validate($videoBlob, $videoConstraints);

        $this->assertTrue(true);
    }

    public function test_it_generates_consistent_cids_for_same_content(): void
    {
        $content = 'Test content';

        $blob1 = $this->handler->storeFromString($content, 'text/plain');
        $blob2 = $this->handler->storeFromString($content, 'text/plain');

        $this->assertEquals($blob1->ref, $blob2->ref);
    }

    public function test_it_generates_different_cids_for_different_content(): void
    {
        $blob1 = $this->handler->storeFromString('Content 1', 'text/plain');
        $blob2 = $this->handler->storeFromString('Content 2', 'text/plain');

        $this->assertNotEquals($blob1->ref, $blob2->ref);
    }

    public function test_it_uses_directory_partitioning_for_storage(): void
    {
        $blob = $this->handler->storeFromString('content', 'text/plain');

        // CID should be used to create partitioned path
        $prefix = substr($blob->ref, 0, 2);
        $middle = substr($blob->ref, 2, 2);
        $expectedPath = "blobs/{$prefix}/{$middle}/{$blob->ref}";

        Storage::disk('local')->assertExists($expectedPath);
    }

    public function test_it_can_change_storage_disk(): void
    {
        Storage::fake('custom');

        $this->handler->setDisk('custom');

        $blob = $this->handler->storeFromString('content', 'text/plain');

        $prefix = substr($blob->ref, 0, 2);
        $middle = substr($blob->ref, 2, 2);
        $expectedPath = "blobs/{$prefix}/{$middle}/{$blob->ref}";

        Storage::disk('custom')->assertExists($expectedPath);
    }

    public function test_it_can_change_base_path(): void
    {
        $this->handler->setBasePath('custom-path');

        $blob = $this->handler->storeFromString('content', 'text/plain');

        $prefix = substr($blob->ref, 0, 2);
        $middle = substr($blob->ref, 2, 2);
        $expectedPath = "custom-path/{$prefix}/{$middle}/{$blob->ref}";

        Storage::disk('local')->assertExists($expectedPath);
    }

    public function test_it_gets_disk_name(): void
    {
        $this->assertEquals('local', $this->handler->getDisk());

        $this->handler->setDisk('custom');

        $this->assertEquals('custom', $this->handler->getDisk());
    }

    public function test_it_gets_base_path(): void
    {
        $this->assertEquals('blobs', $this->handler->getBasePath());

        $this->handler->setBasePath('custom-path');

        $this->assertEquals('custom-path', $this->handler->getBasePath());
    }

    public function test_it_handles_file_without_mime_type(): void
    {
        $file = UploadedFile::fake()->create('unknown.bin');

        $blob = $this->handler->store($file);

        // Should default to application/octet-stream if mime type is null
        $this->assertNotNull($blob->mimeType);
    }

    public function test_it_accepts_constraints_as_array(): void
    {
        $blob = new BlobReference('cid', 'image/png', 1024);

        $constraints = [
            'accept' => ['image/png', 'image/jpeg'], // Array of types
            'maxSize' => 2048,
        ];

        $this->handler->validate($blob, $constraints);

        $this->assertTrue(true);
    }

    public function test_it_stores_binary_content(): void
    {
        $binaryContent = random_bytes(1024);

        $blob = $this->handler->storeFromString($binaryContent, 'application/octet-stream');

        $retrieved = $this->handler->get($blob->ref);

        $this->assertEquals($binaryContent, $retrieved);
    }
}
