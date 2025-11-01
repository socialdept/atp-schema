<?php

namespace SocialDept\Schema;

use Illuminate\Support\ServiceProvider;

class SchemaServiceProvider extends ServiceProvider
{
    /**
     * Register any package services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/schema.php', 'schema');

        // Singleton bindings will be added in subsequent commits as we create the implementations
    }

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<string>
     */
    public function provides(): array
    {
        return ['schema'];
    }

    /**
     * Console-specific booting.
     */
    protected function bootForConsole(): void
    {
        // Publish config
        $this->publishes([
            __DIR__.'/../config/schema.php' => config_path('schema.php'),
        ], 'schema-config');

        // Publish stubs (will be created in later commits)
        $this->publishes([
            __DIR__.'/../stubs' => base_path('stubs/schema'),
        ], 'schema-stubs');
    }
}
