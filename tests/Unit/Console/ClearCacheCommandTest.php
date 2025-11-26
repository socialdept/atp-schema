<?php

namespace SocialDept\AtpSchema\Tests\Unit\Console;

use Illuminate\Support\Facades\Cache;
use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Console\ClearCacheCommand;
use SocialDept\AtpSchema\SchemaServiceProvider;

class ClearCacheCommandTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [SchemaServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        config([
            'schema.cache.prefix' => 'schema',
        ]);
    }

    public function test_it_clears_specific_schema_cache(): void
    {
        // Set a cache value
        Cache::put('schema:parsed:test.nsid', ['data'], 3600);

        $this->artisan(ClearCacheCommand::class, [
            '--nsid' => 'test.nsid',
        ])
            ->expectsOutput('Clearing cache for schema: test.nsid')
            ->expectsOutput('✓ Cache cleared')
            ->assertSuccessful();

        $this->assertFalse(Cache::has('schema:parsed:test.nsid'));
    }

    public function test_it_clears_all_caches(): void
    {
        // Set some cache values
        Cache::put('schema:parsed:test1', ['data'], 3600);
        Cache::put('schema:parsed:test2', ['data'], 3600);

        $this->artisan(ClearCacheCommand::class, [
            '--all' => true,
        ])
            ->expectsOutput('Clearing all schema caches...')
            ->assertSuccessful();
    }

    public function test_it_requires_nsid_or_all_option(): void
    {
        $this->artisan(ClearCacheCommand::class)
            ->expectsOutput('Either --nsid or --all option must be provided')
            ->assertFailed();
    }
}
