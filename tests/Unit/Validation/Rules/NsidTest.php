<?php

namespace SocialDept\AtpSchema\Tests\Unit\Validation\Rules;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Validation\Rules\Nsid;

class NsidTest extends TestCase
{
    protected Nsid $rule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rule = new Nsid();
    }

    public function test_it_validates_valid_nsid(): void
    {
        $valid = 'app.bsky.feed.post';

        $failed = false;
        $this->rule->validate('nsid', $valid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertFalse($failed);
    }

    public function test_it_rejects_invalid_nsid(): void
    {
        $invalid = 'invalid-nsid';

        $failed = false;
        $this->rule->validate('nsid', $invalid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }

    public function test_it_rejects_non_string(): void
    {
        $failed = false;
        $this->rule->validate('nsid', 123, function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }

    public function test_it_validates_various_nsids(): void
    {
        $validNsids = [
            'com.example.test',
            'app.bsky.feed.post',
            'io.github.user.action',
        ];

        foreach ($validNsids as $nsid) {
            $failed = false;
            $this->rule->validate('nsid', $nsid, function () use (&$failed) {
                $failed = true;
            });

            $this->assertFalse($failed, "Expected {$nsid} to be valid");
        }
    }
}
