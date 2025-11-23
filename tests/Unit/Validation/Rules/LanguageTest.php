<?php

namespace SocialDept\Schema\Tests\Unit\Validation\Rules;

use Orchestra\Testbench\TestCase;
use SocialDept\Schema\Validation\Rules\Language;

class LanguageTest extends TestCase
{
    protected Language $rule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rule = new Language();
    }

    public function test_it_validates_simple_language_code(): void
    {
        $valid = 'en';

        $failed = false;
        $this->rule->validate('language', $valid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertFalse($failed);
    }

    public function test_it_validates_language_with_region(): void
    {
        $valid = 'en-US';

        $failed = false;
        $this->rule->validate('language', $valid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertFalse($failed);
    }

    public function test_it_validates_language_with_script(): void
    {
        $valid = 'zh-Hans';

        $failed = false;
        $this->rule->validate('language', $valid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertFalse($failed);
    }

    public function test_it_validates_complex_language_tags(): void
    {
        $validLanguages = [
            'en',
            'en-US',
            'zh-Hans',
            'zh-Hans-CN',
            'en-GB',
            'es-419',
            'fr-CA',
        ];

        foreach ($validLanguages as $language) {
            $failed = false;
            $this->rule->validate('language', $language, function () use (&$failed) {
                $failed = true;
            });

            $this->assertFalse($failed, "Expected {$language} to be valid");
        }
    }

    public function test_it_rejects_invalid_language_code(): void
    {
        $invalid = 'not-a-language-123';

        $failed = false;
        $this->rule->validate('language', $invalid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }

    public function test_it_rejects_too_short_code(): void
    {
        $invalid = 'e';

        $failed = false;
        $this->rule->validate('language', $invalid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }

    public function test_it_rejects_non_string(): void
    {
        $failed = false;
        $this->rule->validate('language', 123, function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }
}
