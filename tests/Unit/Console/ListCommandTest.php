<?php

namespace SocialDept\AtpSchema\Tests\Unit\Console;

use Orchestra\Testbench\TestCase;
use SocialDept\AtpSchema\Console\ListCommand;
use SocialDept\AtpSchema\SchemaServiceProvider;

class ListCommandTest extends TestCase
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
        ]);
    }

    public function test_it_lists_schemas(): void
    {
        $this->artisan(ListCommand::class)
            ->assertSuccessful();
    }

    public function test_it_filters_schemas_by_pattern(): void
    {
        $this->artisan(ListCommand::class, [
            '--filter' => 'app.bsky.*',
        ])
            ->assertSuccessful();
    }

    public function test_it_filters_schemas_by_type(): void
    {
        $this->artisan(ListCommand::class, [
            '--type' => 'record',
        ])
            ->assertSuccessful();
    }
}
