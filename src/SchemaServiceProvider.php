<?php

namespace SocialDept\AtpSchema;

use Illuminate\Support\ServiceProvider;
use SocialDept\AtpSchema\Support\PathHelper;

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
                cacheTtl: config('schema.cache.schema_ttl', 3600),
                cachePrefix: config('schema.cache.prefix', 'schema'),
                dnsResolutionEnabled: config('schema.dns_resolution.enabled', true),
                httpTimeout: config('schema.http.timeout', 10)
            );
        });

        // Register LexiconValidator
        $this->app->singleton(Validation\LexiconValidator::class, function ($app) {
            return new Validation\LexiconValidator(
                schemaLoader: $app->make(Parser\SchemaLoader::class)
            );
        });

        // Register NamingConverter
        $this->app->singleton(Generator\NamingConverter::class, function ($app) {
            return new Generator\NamingConverter(
                baseNamespace: PathHelper::pathToNamespace(config('schema.generators.lexicon_path', 'app/Lexicons'))
            );
        });

        // Register NamespaceResolver
        $this->app->singleton(Generator\NamespaceResolver::class, function ($app) {
            return new Generator\NamespaceResolver(
                baseNamespace: PathHelper::pathToNamespace(config('schema.generators.lexicon_path', 'app/Lexicons'))
            );
        });

        // Register UnionResolver
        $this->app->singleton(Services\UnionResolver::class);

        // Register ExtensionManager
        $this->app->singleton(Support\ExtensionManager::class);

        // Register DefaultLexiconParser
        $this->app->singleton(Parser\DefaultLexiconParser::class);

        // Register InMemoryLexiconRegistry
        $this->app->singleton(Parser\InMemoryLexiconRegistry::class);

        // Register DnsLexiconResolver
        $this->app->singleton(Parser\DnsLexiconResolver::class, function ($app) {
            return new Parser\DnsLexiconResolver(
                enabled: config('schema.dns_resolution.enabled', true),
                httpTimeout: config('schema.http.timeout', 10),
                parser: $app->make(Parser\DefaultLexiconParser::class)
            );
        });

        // Register DefaultBlobHandler
        $this->app->singleton(Support\DefaultBlobHandler::class, function ($app) {
            return new Support\DefaultBlobHandler(
                disk: config('schema.blobs.disk'),
                path: config('schema.blobs.path', 'blobs')
            );
        });

        // Bind BlobHandler contract to DefaultBlobHandler
        $this->app->bind(Contracts\BlobHandler::class, Support\DefaultBlobHandler::class);

        // Bind LexiconParser contract to DefaultLexiconParser
        $this->app->bind(Contracts\LexiconParser::class, Parser\DefaultLexiconParser::class);

        // Bind LexiconRegistry contract to InMemoryLexiconRegistry
        $this->app->bind(Contracts\LexiconRegistry::class, Parser\InMemoryLexiconRegistry::class);

        // Bind LexiconResolver contract to DnsLexiconResolver
        $this->app->bind(Contracts\LexiconResolver::class, Parser\DnsLexiconResolver::class);

        // Bind SchemaRepository contract to SchemaLoader
        $this->app->bind(Contracts\SchemaRepository::class, Parser\SchemaLoader::class);

        // Register DTOGenerator
        $this->app->singleton(Generator\DTOGenerator::class, function ($app) {
            return new Generator\DTOGenerator(
                schemaLoader: $app->make(Parser\SchemaLoader::class),
                baseNamespace: PathHelper::pathToNamespace(config('schema.generators.lexicon_path', 'app/Lexicons')),
                outputDirectory: base_path(config('schema.generators.lexicon_path', 'app/Lexicons')),
                typeParser: null,
                namespaceResolver: $app->make(Generator\NamespaceResolver::class)
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
        $this->bootValidationRules();

        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register custom validation rules.
     */
    protected function bootValidationRules(): void
    {
        $validator = $this->app->make('validator');

        // Register AT Protocol validation rules
        $validator->extend('nsid', function ($attribute, $value) {
            $rule = new Validation\Rules\Nsid();
            $failed = false;
            $rule->validate($attribute, $value, function () use (&$failed) {
                $failed = true;
            });

            return ! $failed;
        }, 'The :attribute is not a valid NSID.');

        $validator->extend('did', function ($attribute, $value) {
            $rule = new Validation\Rules\Did();
            $failed = false;
            $rule->validate($attribute, $value, function () use (&$failed) {
                $failed = true;
            });

            return ! $failed;
        }, 'The :attribute is not a valid DID.');

        $validator->extend('handle', function ($attribute, $value) {
            $rule = new Validation\Rules\Handle();
            $failed = false;
            $rule->validate($attribute, $value, function () use (&$failed) {
                $failed = true;
            });

            return ! $failed;
        }, 'The :attribute is not a valid handle.');

        $validator->extend('at_uri', function ($attribute, $value) {
            $rule = new Validation\Rules\AtUri();
            $failed = false;
            $rule->validate($attribute, $value, function () use (&$failed) {
                $failed = true;
            });

            return ! $failed;
        }, 'The :attribute is not a valid AT URI.');

        $validator->extend('at_datetime', function ($attribute, $value) {
            $rule = new Validation\Rules\AtDatetime();
            $failed = false;
            $rule->validate($attribute, $value, function () use (&$failed) {
                $failed = true;
            });

            return ! $failed;
        }, 'The :attribute is not a valid AT Protocol datetime.');

        $validator->extend('cid', function ($attribute, $value) {
            $rule = new Validation\Rules\Cid();
            $failed = false;
            $rule->validate($attribute, $value, function () use (&$failed) {
                $failed = true;
            });

            return ! $failed;
        }, 'The :attribute is not a valid CID.');

        $validator->extend('max_graphemes', function ($attribute, $value, $parameters) {
            if (empty($parameters)) {
                return false;
            }
            $rule = new Validation\Rules\MaxGraphemes((int) $parameters[0]);
            $failed = false;
            $rule->validate($attribute, $value, function () use (&$failed) {
                $failed = true;
            });

            return ! $failed;
        }, 'The :attribute may not be greater than :max_graphemes graphemes.');

        $validator->extend('min_graphemes', function ($attribute, $value, $parameters) {
            if (empty($parameters)) {
                return false;
            }
            $rule = new Validation\Rules\MinGraphemes((int) $parameters[0]);
            $failed = false;
            $rule->validate($attribute, $value, function () use (&$failed) {
                $failed = true;
            });

            return ! $failed;
        }, 'The :attribute must be at least :min_graphemes graphemes.');

        $validator->extend('language', function ($attribute, $value) {
            $rule = new Validation\Rules\Language();
            $failed = false;
            $rule->validate($attribute, $value, function () use (&$failed) {
                $failed = true;
            });

            return ! $failed;
        }, 'The :attribute is not a valid BCP 47 language code.');

        // Register replacements for parameterized rules
        $validator->replacer('max_graphemes', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':max_graphemes', $parameters[0], $message);
        });

        $validator->replacer('min_graphemes', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':min_graphemes', $parameters[0], $message);
        });
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

        // Publish lexicon JSON files
        $this->publishes([
            __DIR__.'/../resources/lexicons' => resource_path('lexicons'),
        ], 'atp-lexicons');

        // Register commands
        $this->commands([
            Console\GenerateCommand::class,
            Console\ValidateCommand::class,
            Console\ListCommand::class,
            Console\ClearCacheCommand::class,
        ]);
    }
}
