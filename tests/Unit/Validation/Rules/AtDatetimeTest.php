<?php

namespace SocialDept\Schema\Tests\Unit\Validation\Rules;

use Orchestra\Testbench\TestCase;
use SocialDept\Schema\Validation\Rules\AtDatetime;

class AtDatetimeTest extends TestCase
{
    protected AtDatetime $rule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rule = new AtDatetime();
    }

    public function test_it_validates_valid_datetime_with_z(): void
    {
        $valid = '2024-01-01T00:00:00Z';

        $failed = false;
        $this->rule->validate('datetime', $valid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertFalse($failed);
    }

    public function test_it_validates_datetime_with_milliseconds(): void
    {
        $valid = '2024-01-01T12:34:56.789Z';

        $failed = false;
        $this->rule->validate('datetime', $valid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertFalse($failed);
    }

    public function test_it_validates_various_datetime_formats(): void
    {
        $validDatetimes = [
            '2024-01-01T00:00:00Z',
            '2024-12-31T23:59:59Z',
            '2024-06-15T12:30:45Z',
        ];

        foreach ($validDatetimes as $datetime) {
            $failed = false;
            $this->rule->validate('datetime', $datetime, function () use (&$failed) {
                $failed = true;
            });

            $this->assertFalse($failed, "Expected {$datetime} to be valid");
        }
    }

    public function test_it_rejects_invalid_datetime(): void
    {
        $invalid = 'not-a-datetime';

        $failed = false;
        $this->rule->validate('datetime', $invalid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }

    public function test_it_rejects_datetime_without_timezone(): void
    {
        $invalid = '2024-01-01T00:00:00';

        $failed = false;
        $this->rule->validate('datetime', $invalid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }

    public function test_it_rejects_non_string(): void
    {
        $failed = false;
        $this->rule->validate('datetime', 123, function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }
}
