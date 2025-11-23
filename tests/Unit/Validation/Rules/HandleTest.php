<?php

namespace SocialDept\Schema\Tests\Unit\Validation\Rules;

use Orchestra\Testbench\TestCase;
use SocialDept\Schema\Validation\Rules\Handle;

class HandleTest extends TestCase
{
    protected Handle $rule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rule = new Handle();
    }

    public function test_it_validates_valid_handle(): void
    {
        $valid = 'user.bsky.social';

        $failed = false;
        $this->rule->validate('handle', $valid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertFalse($failed);
    }

    public function test_it_validates_various_handles(): void
    {
        $validHandles = [
            'example.com',
            'user.bsky.social',
            'my-handle.example.io',
            'test123.domain.org',
        ];

        foreach ($validHandles as $handle) {
            $failed = false;
            $this->rule->validate('handle', $handle, function () use (&$failed) {
                $failed = true;
            });

            $this->assertFalse($failed, "Expected {$handle} to be valid");
        }
    }

    public function test_it_rejects_invalid_handle(): void
    {
        $invalid = 'invalid handle';

        $failed = false;
        $this->rule->validate('handle', $invalid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }

    public function test_it_rejects_too_short_handle(): void
    {
        $invalid = 'ab';

        $failed = false;
        $this->rule->validate('handle', $invalid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }

    public function test_it_rejects_non_string(): void
    {
        $failed = false;
        $this->rule->validate('handle', 123, function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }
}
