<?php

namespace SocialDept\AtpSchema\Tests\Unit\Validation\Rules;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Validation\Rules\Cid;

class CidTest extends TestCase
{
    protected Cid $rule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rule = new Cid();
    }

    public function test_it_validates_cidv0(): void
    {
        $valid = 'QmXg9Pp2ytZ14xgmQjYEiHjVjMFXzCVVEcRTWJBmLgR39V';

        $failed = false;
        $this->rule->validate('cid', $valid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertFalse($failed);
    }

    public function test_it_validates_cidv1_with_base32(): void
    {
        $valid = 'bafybeigdyrzt5sfp7udm7hu76uh7y26nf3efuylqabf3oclgtqy55fbzdi';

        $failed = false;
        $this->rule->validate('cid', $valid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertFalse($failed);
    }

    public function test_it_validates_cidv1_with_base58(): void
    {
        $valid = 'zdj7WhuEjrB52m1BisYCtmjH1hSKa7yZ3jEZ9JcXaFRD51wVz';

        $failed = false;
        $this->rule->validate('cid', $valid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertFalse($failed);
    }

    public function test_it_rejects_invalid_cid(): void
    {
        $invalid = 'not-a-cid';

        $failed = false;
        $this->rule->validate('cid', $invalid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }

    public function test_it_rejects_cidv0_with_wrong_length(): void
    {
        $invalid = 'QmShortCid';

        $failed = false;
        $this->rule->validate('cid', $invalid, function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }

    public function test_it_rejects_non_string(): void
    {
        $failed = false;
        $this->rule->validate('cid', 123, function () use (&$failed) {
            $failed = true;
        });

        $this->assertTrue($failed);
    }
}
