<?php

namespace SocialDept\Schema\Tests\Unit\Validation\Rules;

use Orchestra\Testbench\TestCase;
use SocialDept\Schema\Validation\Rules\AtUri;

class AtUriTest extends TestCase
{
    protected AtUri $rule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rule = new AtUri();
    }

    public function test_it_validates_valid_at_uri_with_did(): void
    {
        $valid = 'at://did:plc:z72i7hdynmk6r22z27h6tvur/app.bsky.feed.post/3jwlwj2ctlk26';

        $failed = false;
        $this->rule->validate('uri', $valid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertFalse($failed);
    }

    public function test_it_validates_valid_at_uri_with_handle(): void
    {
        $valid = 'at://user.bsky.social/app.bsky.feed.post/3jwlwj2ctlk26';

        $failed = false;
        $this->rule->validate('uri', $valid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertFalse($failed);
    }

    public function test_it_validates_at_uri_without_path(): void
    {
        $valid = 'at://did:plc:z72i7hdynmk6r22z27h6tvur';

        $failed = false;
        $this->rule->validate('uri', $valid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertFalse($failed);
    }

    public function test_it_rejects_uri_without_at_protocol(): void
    {
        $invalid = 'https://example.com/path';

        $failed = false;
        $this->rule->validate('uri', $invalid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }

    public function test_it_rejects_invalid_authority(): void
    {
        $invalid = 'at://not a valid authority/path';

        $failed = false;
        $this->rule->validate('uri', $invalid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }

    public function test_it_rejects_empty_uri(): void
    {
        $invalid = 'at://';

        $failed = false;
        $this->rule->validate('uri', $invalid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }

    public function test_it_rejects_non_string(): void
    {
        $failed = false;
        $this->rule->validate('uri', 123, function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }
}
