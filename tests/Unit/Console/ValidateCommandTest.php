<?php

namespace SocialDept\AtpSchema\Tests\Unit\Console;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Console\ValidateCommand;
use SocialDept\AtpSchema\SchemaServiceProvider;

class ValidateCommandTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [SchemaServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'atp-schema.sources' => [__DIR__.'/../../fixtures'],
        ]);
    }

    public function test_it_validates_valid_data(): void
    {
        $data = json_encode([
            'text' => 'Hello, World!',
            'createdAt' => '2024-01-01T00:00:00Z',
        ]);

        $this->artisan(ValidateCommand::class, [
            'nsid' => 'app.bsky.feed.post',
            '--data' => $data,
        ])
            ->expectsOutput('Validating data against schema: app.bsky.feed.post')
            ->expectsOutput('✓ Validation passed')
            ->assertSuccessful();
    }

    public function test_it_fails_on_invalid_data(): void
    {
        $data = json_encode([
            'text' => 'Hello, World!',
            // Missing required createdAt
        ]);

        $this->artisan(ValidateCommand::class, [
            'nsid' => 'app.bsky.feed.post',
            '--data' => $data,
        ])
            ->expectsOutput('✗ Validation failed:')
            ->assertFailed();
    }

    public function test_it_requires_data_or_file_option(): void
    {
        $this->artisan(ValidateCommand::class, [
            'nsid' => 'app.bsky.feed.post',
        ])
            ->expectsOutput('Either --data or --file option must be provided')
            ->assertFailed();
    }

    public function test_it_handles_invalid_json(): void
    {
        $this->artisan(ValidateCommand::class, [
            'nsid' => 'app.bsky.feed.post',
            '--data' => '{invalid json}',
        ])
            ->assertFailed();
    }
}
