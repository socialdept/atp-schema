<?php

namespace SocialDept\Schema\Tests\Unit\Validation\Rules;

use Orchestra\Testbench\TestCase;
use SocialDept\Schema\Validation\Rules\MaxGraphemes;

class MaxGraphemesTest extends TestCase
{
    public function test_it_validates_string_within_limit(): void
    {
        $rule = new MaxGraphemes(10);

        $valid = 'Hello';

        $failed = false;
        $rule->validate('text', $valid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertFalse($failed);
    }

    public function test_it_rejects_string_exceeding_limit(): void
    {
        $rule = new MaxGraphemes(5);

        $invalid = 'This is too long';

        $failed = false;
        $rule->validate('text', $invalid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }

    public function test_it_counts_graphemes_correctly(): void
    {
        $rule = new MaxGraphemes(5);

        // 6 emoji graphemes
        $invalid = '😀😁😂😃😄😅';

        $failed = false;
        $rule->validate('text', $invalid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }

    public function test_it_allows_exact_limit(): void
    {
        $rule = new MaxGraphemes(5);

        $valid = '😀😁😂😃😄';

        $failed = false;
        $rule->validate('text', $valid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertFalse($failed);
    }

    public function test_it_rejects_non_string(): void
    {
        $rule = new MaxGraphemes(10);

        $failed = false;
        $rule->validate('text', 123, function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }
}
