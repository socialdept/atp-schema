<?php

namespace SocialDept\Schema\Tests\Unit\Validation\Rules;

use Orchestra\Testbench\TestCase;
use SocialDept\Schema\Validation\Rules\Did;

class DidTest extends TestCase
{
    protected Did $rule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rule = new Did();
    }

    public function test_it_validates_valid_did(): void
    {
        $valid = 'did:plc:abcdef123456';

        $failed = false;
        $this->rule->validate('did', $valid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertFalse($failed);
    }

    public function test_it_validates_various_did_methods(): void
    {
        $validDids = [
            'did:plc:z72i7hdynmk6r22z27h6tvur',
            'did:web:example.com',
            'did:key:z6MkhaXgBZDvotDkL5257faiztiGiC2QtKLGpbnnEGta2doK',
        ];

        foreach ($validDids as $did) {
            $failed = false;
            $this->rule->validate('did', $did, function () use (&$failed) {
                $failed = true;
            });

            $this->assertFalse($failed, "Expected {$did} to be valid");
        }
    }

    public function test_it_rejects_invalid_did(): void
    {
        $invalid = 'not-a-did';

        $failed = false;
        $this->rule->validate('did', $invalid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }

    public function test_it_rejects_did_without_method(): void
    {
        $invalid = 'did:';

        $failed = false;
        $this->rule->validate('did', $invalid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }

    public function test_it_rejects_non_string(): void
    {
        $failed = false;
        $this->rule->validate('did', 123, function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }
}
