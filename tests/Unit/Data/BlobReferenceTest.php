<?php

namespace SocialDept\Schema\Tests\Unit\Data;

use Orchestra\Testbench\TestCase;
use SocialDept\Schema\Data\BlobReference;
use SocialDept\Schema\Exceptions\SchemaValidationException;

class BlobReferenceTest extends TestCase
{
    public function test_it_creates_from_constructor(): void
    {
        $blob = new BlobReference(
            'bafyreig...',
            'image/png',
            12345
        );

        $this->assertEquals('bafyreig...', $blob->ref);
        $this->assertEquals('image/png', $blob->mimeType);
        $this->assertEquals(12345, $blob->size);
    }

    public function test_it_creates_from_array_with_link_object(): void
    {
        $data = [
            '$type' => 'blob',
            'ref' => [
                '$link' => 'bafyreig...',
            ],
            'mimeType' => 'image/jpeg',
            'size' => 54321,
        ];

        $blob = BlobReference::fromArray($data);

        $this->assertEquals('bafyreig...', $blob->ref);
        $this->assertEquals('image/jpeg', $blob->mimeType);
        $this->assertEquals(54321, $blob->size);
    }

    public function test_it_creates_from_array_with_string_ref(): void
    {
        $data = [
            'ref' => 'bafyreig...',
            'mimeType' => 'video/mp4',
            'size' => 999,
        ];

        $blob = BlobReference::fromArray($data);

        $this->assertEquals('bafyreig...', $blob->ref);
        $this->assertEquals('video/mp4', $blob->mimeType);
        $this->assertEquals(999, $blob->size);
    }

    public function test_it_throws_exception_when_ref_is_missing(): void
    {
        $this->expectException(SchemaValidationException::class);

        BlobReference::fromArray([
            'mimeType' => 'image/png',
            'size' => 123,
        ]);
    }

    public function test_it_throws_exception_when_mime_type_is_missing(): void
    {
        $this->expectException(SchemaValidationException::class);

        BlobReference::fromArray([
            'ref' => 'bafyreig...',
            'size' => 123,
        ]);
    }

    public function test_it_throws_exception_when_size_is_missing(): void
    {
        $this->expectException(SchemaValidationException::class);

        BlobReference::fromArray([
            'ref' => 'bafyreig...',
            'mimeType' => 'image/png',
        ]);
    }

    public function test_it_converts_to_array(): void
    {
        $blob = new BlobReference(
            'bafyreig...',
            'image/png',
            12345
        );

        $array = $blob->toArray();

        $this->assertEquals([
            '$type' => 'blob',
            'ref' => [
                '$link' => 'bafyreig...',
            ],
            'mimeType' => 'image/png',
            'size' => 12345,
        ], $array);
    }

    public function test_it_gets_cid(): void
    {
        $blob = new BlobReference('bafyreig123', 'image/png', 100);

        $this->assertEquals('bafyreig123', $blob->getCid());
    }

    public function test_it_gets_mime_type(): void
    {
        $blob = new BlobReference('bafyreig...', 'video/webm', 100);

        $this->assertEquals('video/webm', $blob->getMimeType());
    }

    public function test_it_gets_size(): void
    {
        $blob = new BlobReference('bafyreig...', 'image/png', 54321);

        $this->assertEquals(54321, $blob->getSize());
    }

    public function test_it_checks_if_is_image(): void
    {
        $imageBlob = new BlobReference('cid1', 'image/png', 100);
        $videoBlob = new BlobReference('cid2', 'video/mp4', 200);

        $this->assertTrue($imageBlob->isImage());
        $this->assertFalse($videoBlob->isImage());
    }

    public function test_it_checks_if_is_video(): void
    {
        $imageBlob = new BlobReference('cid1', 'image/png', 100);
        $videoBlob = new BlobReference('cid2', 'video/mp4', 200);

        $this->assertFalse($imageBlob->isVideo());
        $this->assertTrue($videoBlob->isVideo());
    }

    public function test_it_matches_exact_mime_type(): void
    {
        $blob = new BlobReference('cid', 'image/png', 100);

        $this->assertTrue($blob->matchesMimeType('image/png'));
        $this->assertFalse($blob->matchesMimeType('image/jpeg'));
    }

    public function test_it_matches_wildcard_mime_type(): void
    {
        $imageBlob = new BlobReference('cid1', 'image/png', 100);
        $videoBlob = new BlobReference('cid2', 'video/mp4', 200);

        $this->assertTrue($imageBlob->matchesMimeType('image/*'));
        $this->assertFalse($imageBlob->matchesMimeType('video/*'));
        $this->assertTrue($videoBlob->matchesMimeType('video/*'));
        $this->assertFalse($videoBlob->matchesMimeType('image/*'));
    }

    public function test_it_matches_universal_wildcard(): void
    {
        $blob = new BlobReference('cid', 'application/json', 100);

        $this->assertTrue($blob->matchesMimeType('*/*'));
    }

    public function test_it_handles_different_image_types(): void
    {
        $pngBlob = new BlobReference('cid1', 'image/png', 100);
        $jpegBlob = new BlobReference('cid2', 'image/jpeg', 200);
        $webpBlob = new BlobReference('cid3', 'image/webp', 300);

        $this->assertTrue($pngBlob->isImage());
        $this->assertTrue($jpegBlob->isImage());
        $this->assertTrue($webpBlob->isImage());
    }

    public function test_it_handles_different_video_types(): void
    {
        $mp4Blob = new BlobReference('cid1', 'video/mp4', 100);
        $webmBlob = new BlobReference('cid2', 'video/webm', 200);

        $this->assertTrue($mp4Blob->isVideo());
        $this->assertTrue($webmBlob->isVideo());
    }

    public function test_it_converts_size_to_integer(): void
    {
        $data = [
            'ref' => 'cid',
            'mimeType' => 'image/png',
            'size' => '12345',
        ];

        $blob = BlobReference::fromArray($data);

        $this->assertIsInt($blob->size);
        $this->assertEquals(12345, $blob->size);
    }
}
