<?php

namespace SocialDept\Schema\Tests\Unit\Console;

use Orchestra\Testbench\TestCase;
use SocialDept\Schema\Console\GenerateCommand;
use SocialDept\Schema\SchemaServiceProvider;

class GenerateCommandTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [SchemaServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'schema.sources' => [__DIR__.'/../../fixtures'],
            'schema.generation.output_directory' => sys_get_temp_dir().'/schema-test',
            'schema.generation.base_namespace' => 'Test\\Generated',
        ]);
    }

    public function test_it_generates_code_in_dry_run_mode(): void
    {
        $this->artisan(GenerateCommand::class, [
            'nsid' => 'app.bsky.feed.post',
            '--dry-run' => true,
        ])
            ->expectsOutput('Generating DTO classes for schema: app.bsky.feed.post')
            ->expectsOutput('Dry run mode - no files will be written')
            ->assertSuccessful();
    }

    public function test_it_handles_invalid_nsid(): void
    {
        $this->artisan(GenerateCommand::class, [
            'nsid' => 'nonexistent.schema',
        ])
            ->expectsOutput('Generating DTO classes for schema: nonexistent.schema')
            ->assertFailed();
    }

    public function test_it_accepts_custom_output_directory(): void
    {
        $this->artisan(GenerateCommand::class, [
            'nsid' => 'app.bsky.feed.post',
            '--output' => '/custom/path',
            '--dry-run' => true,
        ])
            ->assertSuccessful();
    }

    public function test_it_accepts_custom_namespace(): void
    {
        $this->artisan(GenerateCommand::class, [
            'nsid' => 'app.bsky.feed.post',
            '--namespace' => 'Custom\\Namespace',
            '--dry-run' => true,
        ])
            ->assertSuccessful();
    }
}
