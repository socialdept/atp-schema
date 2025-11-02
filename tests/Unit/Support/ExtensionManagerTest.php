<?php

namespace SocialDept\Schema\Tests\Unit\Support;

use Orchestra\Testbench\TestCase;
use SocialDept\Schema\Support\ExtensionManager;

class ExtensionManagerTest extends TestCase
{
    protected ExtensionManager $manager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->manager = new ExtensionManager();
    }

    public function test_it_registers_hook(): void
    {
        $this->manager->hook('test', fn () => 'result');

        $this->assertTrue($this->manager->has('test'));
    }

    public function test_it_executes_hook(): void
    {
        $this->manager->hook('test', fn () => 'result1');
        $this->manager->hook('test', fn () => 'result2');

        $results = $this->manager->execute('test');

        $this->assertEquals(['result1', 'result2'], $results);
    }

    public function test_it_executes_hook_with_arguments(): void
    {
        $this->manager->hook('test', fn ($a, $b) => $a + $b);

        $results = $this->manager->execute('test', 5, 3);

        $this->assertEquals([8], $results);
    }

    public function test_it_returns_empty_array_for_nonexistent_hook(): void
    {
        $results = $this->manager->execute('nonexistent');

        $this->assertEquals([], $results);
    }

    public function test_it_executes_until_first_non_null(): void
    {
        $this->manager->hook('test', fn () => null);
        $this->manager->hook('test', fn () => 'found');
        $this->manager->hook('test', fn () => 'not reached');

        $result = $this->manager->executeUntil('test');

        $this->assertEquals('found', $result);
    }

    public function test_it_returns_null_when_all_hooks_return_null(): void
    {
        $this->manager->hook('test', fn () => null);
        $this->manager->hook('test', fn () => null);

        $result = $this->manager->executeUntil('test');

        $this->assertNull($result);
    }

    public function test_it_returns_null_for_nonexistent_hook_until(): void
    {
        $result = $this->manager->executeUntil('nonexistent');

        $this->assertNull($result);
    }

    public function test_it_filters_value_through_hooks(): void
    {
        $this->manager->hook('test', fn ($value) => $value * 2);
        $this->manager->hook('test', fn ($value) => $value + 10);

        $result = $this->manager->filter('test', 5);

        $this->assertEquals(20, $result); // (5 * 2) + 10
    }

    public function test_it_returns_original_value_when_no_hooks(): void
    {
        $result = $this->manager->filter('nonexistent', 'original');

        $this->assertEquals('original', $result);
    }

    public function test_it_filters_with_additional_arguments(): void
    {
        $this->manager->hook('test', fn ($value, $multiplier) => $value * $multiplier);

        $result = $this->manager->filter('test', 5, 3);

        $this->assertEquals(15, $result);
    }

    public function test_it_checks_if_has_hook(): void
    {
        $this->assertFalse($this->manager->has('test'));

        $this->manager->hook('test', fn () => 'result');

        $this->assertTrue($this->manager->has('test'));
    }

    public function test_it_gets_hook_callbacks(): void
    {
        $callback1 = fn () => 'result1';
        $callback2 = fn () => 'result2';

        $this->manager->hook('test', $callback1);
        $this->manager->hook('test', $callback2);

        $callbacks = $this->manager->get('test');

        $this->assertCount(2, $callbacks);
        $this->assertSame($callback1, $callbacks[0]);
        $this->assertSame($callback2, $callbacks[1]);
    }

    public function test_it_returns_empty_array_for_nonexistent_hook_get(): void
    {
        $callbacks = $this->manager->get('nonexistent');

        $this->assertEquals([], $callbacks);
    }

    public function test_it_removes_hook(): void
    {
        $this->manager->hook('test', fn () => 'result');

        $this->assertTrue($this->manager->has('test'));

        $this->manager->remove('test');

        $this->assertFalse($this->manager->has('test'));
    }

    public function test_it_clears_all_hooks(): void
    {
        $this->manager->hook('test1', fn () => 'result1');
        $this->manager->hook('test2', fn () => 'result2');

        $this->assertTrue($this->manager->has('test1'));
        $this->assertTrue($this->manager->has('test2'));

        $this->manager->clear();

        $this->assertFalse($this->manager->has('test1'));
        $this->assertFalse($this->manager->has('test2'));
    }

    public function test_it_counts_callbacks(): void
    {
        $this->assertEquals(0, $this->manager->count('test'));

        $this->manager->hook('test', fn () => 'result1');

        $this->assertEquals(1, $this->manager->count('test'));

        $this->manager->hook('test', fn () => 'result2');

        $this->assertEquals(2, $this->manager->count('test'));
    }

    public function test_it_gets_hook_names(): void
    {
        $this->manager->hook('test1', fn () => 'result1');
        $this->manager->hook('test2', fn () => 'result2');
        $this->manager->hook('test3', fn () => 'result3');

        $names = $this->manager->names();

        $this->assertEquals(['test1', 'test2', 'test3'], $names);
    }

    public function test_it_returns_empty_array_when_no_hooks(): void
    {
        $names = $this->manager->names();

        $this->assertEquals([], $names);
    }

    public function test_it_chains_hook_calls(): void
    {
        $result = $this->manager
            ->hook('test1', fn () => 'result1')
            ->hook('test2', fn () => 'result2');

        $this->assertSame($this->manager, $result);
        $this->assertTrue($this->manager->has('test1'));
        $this->assertTrue($this->manager->has('test2'));
    }

    public function test_it_chains_remove_calls(): void
    {
        $this->manager->hook('test', fn () => 'result');

        $result = $this->manager->remove('test');

        $this->assertSame($this->manager, $result);
    }

    public function test_it_chains_clear_calls(): void
    {
        $result = $this->manager->clear();

        $this->assertSame($this->manager, $result);
    }

    public function test_it_handles_multiple_callbacks_for_same_hook(): void
    {
        $executed = [];

        $this->manager->hook('test', function () use (&$executed) {
            $executed[] = 'first';
        });

        $this->manager->hook('test', function () use (&$executed) {
            $executed[] = 'second';
        });

        $this->manager->hook('test', function () use (&$executed) {
            $executed[] = 'third';
        });

        $this->manager->execute('test');

        $this->assertEquals(['first', 'second', 'third'], $executed);
    }

    public function test_it_handles_complex_filter_chain(): void
    {
        $this->manager->hook('transform', fn ($value) => strtoupper($value));
        $this->manager->hook('transform', fn ($value) => str_replace(' ', '_', $value));
        $this->manager->hook('transform', fn ($value) => $value.'_SUFFIX');

        $result = $this->manager->filter('transform', 'hello world');

        $this->assertEquals('HELLO_WORLD_SUFFIX', $result);
    }

    public function test_it_handles_execute_until_with_arguments(): void
    {
        $this->manager->hook('search', fn ($needle, $haystack) => in_array($needle, $haystack) ? $needle : null);

        $result = $this->manager->executeUntil('search', 'b', ['a', 'b', 'c']);

        $this->assertEquals('b', $result);
    }

    public function test_it_removes_nonexistent_hook_safely(): void
    {
        $result = $this->manager->remove('nonexistent');

        $this->assertSame($this->manager, $result);
    }
}
