<?php

namespace SocialDept\AtpSchema\Tests\Unit\Validation\TypeValidators;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Exceptions\RecordValidationException;
use SocialDept\AtpSchema\Validation\TypeValidators\UnionValidator;

class UnionValidatorTest extends TestCase
{
    protected UnionValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new UnionValidator();
    }

    public function test_it_validates_discriminated_union(): void
    {
        $this->validator->validate(
            ['$type' => 'app.bsky.feed.post'],
            [
                'type' => 'union',
                'refs' => ['app.bsky.feed.post', 'app.bsky.feed.repost'],
                'closed' => true,
            ],
            '$.field'
        );

        $this->assertTrue(true);
    }

    public function test_it_rejects_discriminated_union_without_type(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate(
            ['text' => 'Hello'],
            [
                'type' => 'union',
                'refs' => ['app.bsky.feed.post', 'app.bsky.feed.repost'],
                'closed' => true,
            ],
            '$.field'
        );
    }

    public function test_it_rejects_discriminated_union_with_invalid_type(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate(
            ['$type' => 'app.bsky.feed.invalid'],
            [
                'type' => 'union',
                'refs' => ['app.bsky.feed.post', 'app.bsky.feed.repost'],
                'closed' => true,
            ],
            '$.field'
        );
    }

    public function test_it_rejects_non_object_for_discriminated_union(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate(
            'not an object',
            [
                'type' => 'union',
                'refs' => ['app.bsky.feed.post', 'app.bsky.feed.repost'],
                'closed' => true,
            ],
            '$.field'
        );
    }

    public function test_it_validates_open_union_with_object(): void
    {
        $this->validator->validate(
            ['data' => 'value'],
            [
                'type' => 'union',
                'refs' => ['app.bsky.feed.post', 'app.bsky.feed.repost'],
                'closed' => false,
            ],
            '$.field'
        );

        $this->assertTrue(true);
    }

    public function test_it_validates_open_union_with_string(): void
    {
        $this->validator->validate(
            'some value',
            [
                'type' => 'union',
                'refs' => ['app.bsky.feed.post', 'app.bsky.feed.repost'],
                'closed' => false,
            ],
            '$.field'
        );

        $this->assertTrue(true);
    }

    public function test_it_validates_open_union_with_integer(): void
    {
        $this->validator->validate(
            123,
            [
                'type' => 'union',
                'refs' => ['app.bsky.feed.post', 'app.bsky.feed.repost'],
                'closed' => false,
            ],
            '$.field'
        );

        $this->assertTrue(true);
    }

    public function test_it_rejects_open_union_with_null(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate(
            null,
            [
                'type' => 'union',
                'refs' => ['app.bsky.feed.post', 'app.bsky.feed.repost'],
                'closed' => false,
            ],
            '$.field'
        );
    }

    public function test_it_rejects_union_without_refs(): void
    {
        $this->expectException(RecordValidationException::class);

        $this->validator->validate(
            ['$type' => 'app.bsky.feed.post'],
            [
                'type' => 'union',
                'closed' => true,
            ],
            '$.field'
        );
    }
}
