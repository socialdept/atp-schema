<?php

namespace SocialDept\AtpSchema\Tests\Unit\Data\Types;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Data\Types\BytesType;
use SocialDept\AtpSchema\Exceptions\RecordValidationException;

class BytesTypeTest extends TestCase
{
    public function test_it_creates_from_array(): void
    {
        $type = BytesType::fromArray([
            'type' => 'bytes',
            'description' => 'Binary data',
            'minLength' => 1,
            'maxLength' => 100,
        ]);

        $this->assertSame('bytes', $type->type);
        $this->assertSame('Binary data', $type->description);
        $this->assertSame(1, $type->minLength);
        $this->assertSame(100, $type->maxLength);
    }

    public function test_it_converts_to_array(): void
    {
        $type = new BytesType(
            description: 'Binary data',
            minLength: 1,
            maxLength: 100
        );

        $array = $type->toArray();

        $this->assertSame('bytes', $array['type']);
        $this->assertSame('Binary data', $array['description']);
        $this->assertSame(1, $array['minLength']);
        $this->assertSame(100, $array['maxLength']);
    }

    public function test_it_validates_bytes_type(): void
    {
        $type = new BytesType();

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage("Expected type 'bytes (base64 string)' at 'field' but got 'integer'");

        $type->validate(123, 'field');
    }

    public function test_it_validates_valid_base64(): void
    {
        $type = new BytesType();

        $base64 = base64_encode('Hello, World!');
        $type->validate($base64, 'field');

        $this->assertTrue(true);
    }

    public function test_it_rejects_invalid_base64(): void
    {
        $type = new BytesType();

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must be valid base64-encoded data');

        $type->validate('not valid base64!!!', 'field');
    }

    public function test_it_validates_min_length(): void
    {
        $type = new BytesType(minLength: 10);

        $type->validate(base64_encode('1234567890'), 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must be at least 10 bytes');

        $type->validate(base64_encode('short'), 'field');
    }

    public function test_it_validates_max_length(): void
    {
        $type = new BytesType(maxLength: 10);

        $type->validate(base64_encode('1234567890'), 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must be at most 10 bytes');

        $type->validate(base64_encode('this is a very long string that exceeds the limit'), 'field');
    }
}
