<?php

namespace SocialDept\Schema\Tests\Unit\Data\Types;

use Orchestra\Testbench\TestCase;
use SocialDept\Schema\Data\Types\BlobType;
use SocialDept\Schema\Exceptions\RecordValidationException;

class BlobTypeTest extends TestCase
{
    public function test_it_creates_from_array(): void
    {
        $type = BlobType::fromArray([
            'type' => 'blob',
            'description' => 'A blob',
            'accept' => ['image/png', 'image/jpeg'],
            'maxSize' => 1000000,
        ]);

        $this->assertSame('blob', $type->type);
        $this->assertSame('A blob', $type->description);
        $this->assertSame(['image/png', 'image/jpeg'], $type->accept);
        $this->assertSame(1000000, $type->maxSize);
    }

    public function test_it_converts_to_array(): void
    {
        $type = new BlobType(
            accept: ['image/png'],
            maxSize: 1000000,
            description: 'A blob'
        );

        $array = $type->toArray();

        $this->assertSame('blob', $array['type']);
        $this->assertSame('A blob', $array['description']);
        $this->assertSame(['image/png'], $array['accept']);
        $this->assertSame(1000000, $array['maxSize']);
    }

    public function test_it_validates_blob_type(): void
    {
        $type = new BlobType();

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage("Expected type 'blob (object)' at 'field' but got 'string'");

        $type->validate('not an object', 'field');
    }

    public function test_it_validates_type_property(): void
    {
        $type = new BlobType();

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must have $type property set to "blob"');

        $type->validate(['ref' => 'cid123'], 'field');
    }

    public function test_it_validates_ref_property(): void
    {
        $type = new BlobType();

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must have ref property');

        $type->validate(['$type' => 'blob'], 'field');
    }

    public function test_it_validates_mime_type_property(): void
    {
        $type = new BlobType();

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must have mimeType property');

        $type->validate(['$type' => 'blob', 'ref' => 'cid123'], 'field');
    }

    public function test_it_validates_size_property(): void
    {
        $type = new BlobType();

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must have size property');

        $type->validate(['$type' => 'blob', 'ref' => 'cid123', 'mimeType' => 'image/png'], 'field');
    }

    public function test_it_validates_valid_blob(): void
    {
        $type = new BlobType();

        $type->validate([
            '$type' => 'blob',
            'ref' => 'cid123',
            'mimeType' => 'image/png',
            'size' => 12345,
        ], 'field');

        $this->assertTrue(true);
    }

    public function test_it_validates_accepted_mime_types(): void
    {
        $type = new BlobType(accept: ['image/png', 'image/jpeg']);

        $type->validate([
            '$type' => 'blob',
            'ref' => 'cid123',
            'mimeType' => 'image/png',
            'size' => 12345,
        ], 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': MIME type must be one of: image/png, image/jpeg');

        $type->validate([
            '$type' => 'blob',
            'ref' => 'cid123',
            'mimeType' => 'image/gif',
            'size' => 12345,
        ], 'field');
    }

    public function test_it_validates_wildcard_mime_types(): void
    {
        $type = new BlobType(accept: ['image/*']);

        $type->validate([
            '$type' => 'blob',
            'ref' => 'cid123',
            'mimeType' => 'image/png',
            'size' => 12345,
        ], 'field');

        $type->validate([
            '$type' => 'blob',
            'ref' => 'cid123',
            'mimeType' => 'image/jpeg',
            'size' => 12345,
        ], 'field');

        $this->expectException(RecordValidationException::class);

        $type->validate([
            '$type' => 'blob',
            'ref' => 'cid123',
            'mimeType' => 'video/mp4',
            'size' => 12345,
        ], 'field');
    }

    public function test_it_validates_max_size(): void
    {
        $type = new BlobType(maxSize: 10000);

        $type->validate([
            '$type' => 'blob',
            'ref' => 'cid123',
            'mimeType' => 'image/png',
            'size' => 10000,
        ], 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': size must not exceed 10000 bytes');

        $type->validate([
            '$type' => 'blob',
            'ref' => 'cid123',
            'mimeType' => 'image/png',
            'size' => 10001,
        ], 'field');
    }

    public function test_it_validates_size_is_integer(): void
    {
        $type = new BlobType(maxSize: 10000);

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': size must be an integer');

        $type->validate([
            '$type' => 'blob',
            'ref' => 'cid123',
            'mimeType' => 'image/png',
            'size' => '12345',
        ], 'field');
    }
}
