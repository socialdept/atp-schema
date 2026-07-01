<?php

namespace SocialDept\AtpSchema\Tests\Unit\Support;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Support\UnionHelper;

/**
 * A union variant whose discriminator is a canonical def fragment (`nsid#def`).
 */
class FragmentVariant extends Data
{
    public function __construct(public readonly string $value = '') {}

    public static function getLexicon(): string
    {
        return 'app.test.embed#view';
    }

    public static function fromArray(array $data): static
    {
        return new static($data['value'] ?? '');
    }
}

class UnionHelperTest extends TestCase
{
    public function test_it_resolves_a_variant_by_its_canonical_fragment_type(): void
    {
        $resolved = UnionHelper::resolveClosedUnion(
            ['$type' => 'app.test.embed#view', 'value' => 'hi'],
            [FragmentVariant::class],
        );

        $this->assertInstanceOf(FragmentVariant::class, $resolved);
        $this->assertSame('hi', $resolved->value);
    }

    public function test_it_still_resolves_records_written_with_the_legacy_dotted_type(): void
    {
        // Records persisted before def $types were canonicalised carry the dotted
        // form; they must still resolve to the same variant.
        $resolved = UnionHelper::resolveClosedUnion(
            ['$type' => 'app.test.embed.view', 'value' => 'legacy'],
            [FragmentVariant::class],
        );

        $this->assertInstanceOf(FragmentVariant::class, $resolved);
        $this->assertSame('legacy', $resolved->value);
    }
}
