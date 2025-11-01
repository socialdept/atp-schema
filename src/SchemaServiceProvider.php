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

        // Register SchemaLoader
        $this->app->singleton(Parser\SchemaLoader::class, function ($app) {
            return new Parser\SchemaLoader(
                sources: config('schema.sources', []),
                useCache: config('schema.cache.enabled', true),
                cacheTtl: config('schema.cache.ttl', 3600),
                cachePrefix: config('schema.cache.prefix', 'schema')
            );
        });

        // Register LexiconValidator
        $this->app->singleton(Validation\LexiconValidator::class, function ($app) {
            return new Validation\LexiconValidator(
                schemaLoader: $app->make(Parser\SchemaLoader::class)
            );
        });

        // Register DTOGenerator
        $this->app->singleton(Generator\DTOGenerator::class, function ($app) {
            return new Generator\DTOGenerator(
                schemaLoader: $app->make(Parser\SchemaLoader::class),
                baseNamespace: config('schema.generation.base_namespace', 'App\\Lexicon'),
                outputDirectory: config('schema.generation.output_directory', 'app/Lexicon')
            );
        });

        // Register SchemaManager
        $this->app->singleton('schema', function ($app) {
            return new SchemaManager(
                loader: $app->make(Parser\SchemaLoader::class),
                validator: $app->make(Validation\LexiconValidator::class),
                generator: $app->make(Generator\DTOGenerator::class)
            );
        });
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

        // Register commands
        $this->commands([
            Console\GenerateCommand::class,
            Console\ValidateCommand::class,
            Console\ListCommand::class,
            Console\ClearCacheCommand::class,
        ]);
    }
}
