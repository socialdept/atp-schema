<?php

namespace SocialDept\AtpSchema\Tests\Unit\Validation\TypeValidators;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Exceptions\RecordValidationException;
use SocialDept\AtpSchema\Validation\TypeValidators\BlobValidator;

class BlobValidatorTest extends TestCase
{
    protected BlobValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new BlobValidator();
    }

    public function test_it_validates_valid_blob(): void
    {
        $this->validator->validate(
            [
                '$type' => 'blob',
                'ref' => 'bafybeigdyrzt5sfp7udm7hu76uh7y26nf3efuylqabf3oclgtqy55fbzdi',
                'mimeType' => 'image/png',
                'size' => 1024,
            ],
            ['type' => 'blob'],
            '$.field'
        );

        $this->assertTrue(true);
    }

    public function test_it_rejects_non_array_blob(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate('not a blob', ['type' => 'blob'], '$.field');
    }

    public function test_it_rejects_blob_without_type_field(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate(
            [
                'ref' => 'bafybeigdyrzt5sfp7udm7hu76uh7y26nf3efuylqabf3oclgtqy55fbzdi',
                'mimeType' => 'image/png',
                'size' => 1024,
            ],
            ['type' => 'blob'],
            '$.field'
        );
    }

    public function test_it_rejects_blob_without_ref(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate(
            [
                '$type' => 'blob',
                'mimeType' => 'image/png',
                'size' => 1024,
            ],
            ['type' => 'blob'],
            '$.field'
        );
    }

    public function test_it_rejects_blob_without_mime_type(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate(
            [
                '$type' => 'blob',
                'ref' => 'bafybeigdyrzt5sfp7udm7hu76uh7y26nf3efuylqabf3oclgtqy55fbzdi',
                'size' => 1024,
            ],
            ['type' => 'blob'],
            '$.field'
        );
    }

    public function test_it_rejects_blob_without_size(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate(
            [
                '$type' => 'blob',
                'ref' => 'bafybeigdyrzt5sfp7udm7hu76uh7y26nf3efuylqabf3oclgtqy55fbzdi',
                'mimeType' => 'image/png',
            ],
            ['type' => 'blob'],
            '$.field'
        );
    }

    public function test_it_validates_accepted_mime_type(): void
    {
        $this->validator->validate(
            [
                '$type' => 'blob',
                'ref' => 'bafybeigdyrzt5sfp7udm7hu76uh7y26nf3efuylqabf3oclgtqy55fbzdi',
                'mimeType' => 'image/png',
                'size' => 1024,
            ],
            ['type' => 'blob', 'accept' => ['image/png', 'image/jpeg']],
            '$.field'
        );

        $this->assertTrue(true);
    }

    public function test_it_rejects_unaccepted_mime_type(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate(
            [
                '$type' => 'blob',
                'ref' => 'bafybeigdyrzt5sfp7udm7hu76uh7y26nf3efuylqabf3oclgtqy55fbzdi',
                'mimeType' => 'video/mp4',
                'size' => 1024,
            ],
            ['type' => 'blob', 'accept' => ['image/png', 'image/jpeg']],
            '$.field'
        );
    }

    public function test_it_validates_wildcard_mime_type(): void
    {
        $this->validator->validate(
            [
                '$type' => 'blob',
                'ref' => 'bafybeigdyrzt5sfp7udm7hu76uh7y26nf3efuylqabf3oclgtqy55fbzdi',
                'mimeType' => 'image/webp',
                'size' => 1024,
            ],
            ['type' => 'blob', 'accept' => ['image/*']],
            '$.field'
        );

        $this->assertTrue(true);
    }

    public function test_it_validates_max_size(): void
    {
        $this->validator->validate(
            [
                '$type' => 'blob',
                'ref' => 'bafybeigdyrzt5sfp7udm7hu76uh7y26nf3efuylqabf3oclgtqy55fbzdi',
                'mimeType' => 'image/png',
                'size' => 1024,
            ],
            ['type' => 'blob', 'maxSize' => 2048],
            '$.field'
        );

        $this->assertTrue(true);
    }

    public function test_it_rejects_blob_exceeding_max_size(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate(
            [
                '$type' => 'blob',
                'ref' => 'bafybeigdyrzt5sfp7udm7hu76uh7y26nf3efuylqabf3oclgtqy55fbzdi',
                'mimeType' => 'image/png',
                'size' => 3000,
            ],
            ['type' => 'blob', 'maxSize' => 2048],
            '$.field'
        );
    }
}
