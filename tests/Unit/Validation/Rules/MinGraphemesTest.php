<?php

namespace SocialDept\AtpSchema\Tests\Unit\Validation\Rules;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Validation\Rules\MinGraphemes;

class MinGraphemesTest extends TestCase
{
    public function test_it_validates_string_meeting_minimum(): void
    {
        $rule = new MinGraphemes(5);

        $valid = 'Hello World';

        $failed = false;
        $rule->validate('text', $valid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertFalse($failed);
    }

    public function test_it_rejects_string_below_minimum(): void
    {
        $rule = new MinGraphemes(10);

        $invalid = 'Short';

        $failed = false;
        $rule->validate('text', $invalid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }

    public function test_it_counts_graphemes_correctly(): void
    {
        $rule = new MinGraphemes(5);

        // 3 emoji graphemes
        $invalid = '😀😁😂';

        $failed = false;
        $rule->validate('text', $invalid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }

    public function test_it_allows_exact_minimum(): void
    {
        $rule = new MinGraphemes(5);

        $valid = '😀😁😂😃😄';

        $failed = false;
        $rule->validate('text', $valid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertFalse($failed);
    }

    public function test_it_rejects_non_string(): void
    {
        $rule = new MinGraphemes(5);

        $failed = false;
        $rule->validate('text', 123, function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }
}
