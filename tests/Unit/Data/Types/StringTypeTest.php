<?php

namespace SocialDept\AtpSchema\Tests\Unit\Data\Types;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Data\Types\StringType;
use SocialDept\AtpSchema\Exceptions\RecordValidationException;

class StringTypeTest extends TestCase
{
    public function test_it_creates_from_array(): void
    {
        $type = StringType::fromArray([
            'type' => 'string',
            'description' => 'A string value',
            'minLength' => 1,
            'maxLength' => 100,
            'minGraphemes' => 1,
            'maxGraphemes' => 50,
            'format' => 'datetime',
            'enum' => ['foo', 'bar'],
            'const' => 'foo',
            'knownValues' => ['foo', 'bar', 'baz'],
        ]);

        $this->assertSame('string', $type->type);
        $this->assertSame('A string value', $type->description);
        $this->assertSame(1, $type->minLength);
        $this->assertSame(100, $type->maxLength);
        $this->assertSame(1, $type->minGraphemes);
        $this->assertSame(50, $type->maxGraphemes);
        $this->assertSame('datetime', $type->format);
        $this->assertSame(['foo', 'bar'], $type->enum);
        $this->assertSame('foo', $type->const);
        $this->assertSame(['foo', 'bar', 'baz'], $type->knownValues);
    }

    public function test_it_converts_to_array(): void
    {
        $type = new StringType(
            description: 'A string value',
            minLength: 1,
            maxLength: 100,
            format: 'datetime'
        );

        $array = $type->toArray();

        $this->assertSame('string', $array['type']);
        $this->assertSame('A string value', $array['description']);
        $this->assertSame(1, $array['minLength']);
        $this->assertSame(100, $array['maxLength']);
        $this->assertSame('datetime', $array['format']);
    }

    public function test_it_validates_string_type(): void
    {
        $type = new StringType();

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage("Expected type 'string' at 'field' but got 'integer'");

        $type->validate(123, 'field');
    }

    public function test_it_validates_const(): void
    {
        $type = new StringType(const: 'foo');

        $type->validate('foo', 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage("Invalid value at 'field': must equal 'foo'");

        $type->validate('bar', 'field');
    }

    public function test_it_validates_enum(): void
    {
        $type = new StringType(enum: ['foo', 'bar']);

        $type->validate('foo', 'field');
        $type->validate('bar', 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must be one of: foo, bar');

        $type->validate('baz', 'field');
    }

    public function test_it_validates_min_length(): void
    {
        $type = new StringType(minLength: 5);

        $type->validate('hello', 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must be at least 5 bytes');

        $type->validate('hi', 'field');
    }

    public function test_it_validates_max_length(): void
    {
        $type = new StringType(maxLength: 5);

        $type->validate('hello', 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must be at most 5 bytes');

        $type->validate('hello world', 'field');
    }

    public function test_it_validates_min_graphemes(): void
    {
        $type = new StringType(minGraphemes: 3);

        $type->validate('abc', 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must be at least 3 graphemes');

        $type->validate('ab', 'field');
    }

    public function test_it_validates_max_graphemes(): void
    {
        $type = new StringType(maxGraphemes: 3);

        $type->validate('abc', 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must be at most 3 graphemes');

        $type->validate('abcd', 'field');
    }

    public function test_it_validates_datetime_format(): void
    {
        $type = new StringType(format: 'datetime');

        $type->validate('2024-01-01T00:00:00Z', 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must be a valid ISO 8601 datetime');

        $type->validate('not a datetime', 'field');
    }

    public function test_it_validates_uri_format(): void
    {
        $type = new StringType(format: 'uri');

        $type->validate('https://example.com', 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must be a valid URI');

        $type->validate('not a uri', 'field');
    }

    public function test_it_validates_at_uri_format(): void
    {
        $type = new StringType(format: 'at-uri');

        $type->validate('at://did:plc:123/app.bsky.feed.post/123', 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must be a valid AT URI');

        $type->validate('https://example.com', 'field');
    }

    public function test_it_validates_did_format(): void
    {
        $type = new StringType(format: 'did');

        $type->validate('did:plc:123abc', 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must be a valid DID');

        $type->validate('not a did', 'field');
    }

    public function test_it_validates_handle_format(): void
    {
        $type = new StringType(format: 'handle');

        $type->validate('alice.bsky.social', 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must be a valid handle');

        $type->validate('invalid handle!', 'field');
    }

    public function test_it_validates_at_identifier_format(): void
    {
        $type = new StringType(format: 'at-identifier');

        $type->validate('did:plc:123abc', 'field');
        $type->validate('alice.bsky.social', 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must be a valid AT identifier (DID or handle)');

        $type->validate('invalid!', 'field');
    }

    public function test_it_validates_nsid_format(): void
    {
        $type = new StringType(format: 'nsid');

        $type->validate('app.bsky.feed.post', 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must be a valid NSID');

        $type->validate('invalid nsid!', 'field');
    }

    public function test_it_validates_cid_format(): void
    {
        $type = new StringType(format: 'cid');

        $type->validate('bafyreihqhqv7h2gfxkj7qxvz7pxqhqvz7h2gfxkj7', 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must be a valid CID');

        $type->validate('invalid cid!', 'field');
    }

    public function test_it_validates_language_format(): void
    {
        $type = new StringType(format: 'language');

        $type->validate('en', 'field');
        $type->validate('en-US', 'field');

        $this->expectException(RecordValidationException::class);
        $this->expectExceptionMessage('Invalid value at \'field\': must be a valid language tag');

        $type->validate('invalid', 'field');
    }
}
