<?php

namespace SocialDept\AtpSchema\Tests\Unit\Authoring;

use PHPUnit\Framework\TestCase;
use SocialDept\AtpSchema\Authoring\LexiconDiff;

class LexiconDiffTest extends TestCase
{
    public function test_changes_are_listed_by_path(): void
    {
        $before = ['id' => 'com.example.thing', 'defs' => ['main' => ['type' => 'record', 'description' => 'Old', 'key' => 'tid']]];
        $after = ['id' => 'com.example.thing', 'defs' => ['main' => ['type' => 'record', 'description' => 'New', 'record' => ['type' => 'object']]]];

        $this->assertSame([
            ['path' => 'defs.main.description', 'change' => 'changed', 'before' => 'Old', 'after' => 'New'],
            ['path' => 'defs.main.key', 'change' => 'removed', 'before' => 'tid', 'after' => null],
            ['path' => 'defs.main.record', 'change' => 'added', 'before' => null, 'after' => ['type' => 'object']],
        ], LexiconDiff::between($before, $after));
    }

    public function test_key_order_and_the_record_type_are_not_changes(): void
    {
        $before = ['lexicon' => 1, 'id' => 'com.example.thing', 'defs' => ['main' => ['type' => 'token', 'description' => 'x']]];
        $after = ['$type' => 'com.atproto.lexicon.schema', 'defs' => ['main' => ['description' => 'x', 'type' => 'token']], 'id' => 'com.example.thing', 'lexicon' => 1];

        $this->assertSame([], LexiconDiff::between($before, array_diff_key($after, ['$type' => true])));
        $this->assertTrue(LexiconDiff::same($before, $after));
        $this->assertFalse(LexiconDiff::same($before, ['lexicon' => 1]));
    }
}
