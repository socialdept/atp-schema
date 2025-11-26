<?php

namespace SocialDept\AtpSchema\Tests\Unit\Validation\TypeValidators;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Exceptions\RecordValidationException;
use SocialDept\AtpSchema\Validation\TypeValidators\StringValidator;

class StringValidatorTest extends TestCase
{
    protected StringValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new StringValidator();
    }

    public function test_it_validates_valid_string(): void
    {
        $this->validator->validate('test', ['type' => 'string'], '$.field');

        $this->assertTrue(true);
    }

    public function test_it_rejects_non_string(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate(123, ['type' => 'string'], '$.field');
    }

    public function test_it_validates_max_length(): void
    {
        $this->validator->validate('test', ['type' => 'string', 'maxLength' => 10], '$.field');

        $this->assertTrue(true);
    }

    public function test_it_rejects_string_exceeding_max_length(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate('this is too long', ['type' => 'string', 'maxLength' => 5], '$.field');
    }

    public function test_it_validates_min_length(): void
    {
        $this->validator->validate('test', ['type' => 'string', 'minLength' => 3], '$.field');

        $this->assertTrue(true);
    }

    public function test_it_rejects_string_below_min_length(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate('ab', ['type' => 'string', 'minLength' => 5], '$.field');
    }

    public function test_it_validates_max_graphemes(): void
    {
        $this->validator->validate('😀😁😂', ['type' => 'string', 'maxGraphemes' => 5], '$.field');

        $this->assertTrue(true);
    }

    public function test_it_rejects_string_exceeding_max_graphemes(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate('😀😁😂😃😄😅', ['type' => 'string', 'maxGraphemes' => 5], '$.field');
    }

    public function test_it_validates_min_graphemes(): void
    {
        $this->validator->validate('😀😁😂😃😄', ['type' => 'string', 'minGraphemes' => 3], '$.field');

        $this->assertTrue(true);
    }

    public function test_it_rejects_string_below_min_graphemes(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate('😀😁', ['type' => 'string', 'minGraphemes' => 5], '$.field');
    }

    public function test_it_validates_enum_constraint(): void
    {
        $this->validator->validate('active', [
            'type' => 'string',
            'enum' => ['active', 'inactive', 'pending'],
        ], '$.field');

        $this->assertTrue(true);
    }

    public function test_it_rejects_value_not_in_enum(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate('unknown', [
            'type' => 'string',
            'enum' => ['active', 'inactive', 'pending'],
        ], '$.field');
    }

    public function test_it_validates_const_constraint(): void
    {
        $this->validator->validate('post', ['type' => 'string', 'const' => 'post'], '$.field');

        $this->assertTrue(true);
    }

    public function test_it_rejects_value_not_matching_const(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate('comment', ['type' => 'string', 'const' => 'post'], '$.field');
    }

    public function test_it_validates_datetime_format(): void
    {
        $this->validator->validate('2024-01-01T00:00:00Z', [
            'type' => 'string',
            'format' => 'datetime',
        ], '$.field');

        $this->assertTrue(true);
    }

    public function test_it_rejects_invalid_datetime(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate('not-a-datetime', [
            'type' => 'string',
            'format' => 'datetime',
        ], '$.field');
    }

    public function test_it_validates_uri_format(): void
    {
        $this->validator->validate('https://example.com', [
            'type' => 'string',
            'format' => 'uri',
        ], '$.field');

        $this->assertTrue(true);
    }

    public function test_it_validates_did_format(): void
    {
        $this->validator->validate('did:plc:z72i7hdynmk6r22z27h6tvur', [
            'type' => 'string',
            'format' => 'did',
        ], '$.field');

        $this->assertTrue(true);
    }

    public function test_it_validates_handle_format(): void
    {
        $this->validator->validate('user.bsky.social', [
            'type' => 'string',
            'format' => 'handle',
        ], '$.field');

        $this->assertTrue(true);
    }

    public function test_it_validates_nsid_format(): void
    {
        $this->validator->validate('app.bsky.feed.post', [
            'type' => 'string',
            'format' => 'nsid',
        ], '$.field');

        $this->assertTrue(true);
    }

    public function test_it_validates_cid_format(): void
    {
        $this->validator->validate('bafybeigdyrzt5sfp7udm7hu76uh7y26nf3efuylqabf3oclgtqy55fbzdi', [
            'type' => 'string',
            'format' => 'cid',
        ], '$.field');

        $this->assertTrue(true);
    }

    public function test_it_validates_language_format(): void
    {
        $this->validator->validate('en-US', [
            'type' => 'string',
            'format' => 'language',
        ], '$.field');

        $this->assertTrue(true);
    }

    public function test_it_passes_unknown_formats(): void
    {
        $this->validator->validate('anything', [
            'type' => 'string',
            'format' => 'unknown-format',
        ], '$.field');

        $this->assertTrue(true);
    }
}
