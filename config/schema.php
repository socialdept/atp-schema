<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Schema Sources
    |--------------------------------------------------------------------------
    |
    | Paths to local lexicon directories. Schemas are searched in the order
    | specified below. Use absolute paths or Laravel path helpers.
    |
    */

    'sources' => [
        // Application-specific lexicons
        resource_path('lexicons'),

        // Bundled official lexicons (included with package)
        __DIR__.'/../resources/lexicons',
    ],

    /*
    |--------------------------------------------------------------------------
    | Include Bundled Lexicons
    |--------------------------------------------------------------------------
    |
    | Whether to include the official AT Protocol lexicons bundled with this
    | package. Disable if you want to manage lexicons manually.
    |
    */

    'include_bundled' => env('SCHEMA_INCLUDE_BUNDLED', true),

    /*
    |--------------------------------------------------------------------------
    | Generator Settings
    |--------------------------------------------------------------------------
    |
    | Configure path for the schema:generate command. The namespace is
    | derived from the path (e.g. 'app/Lexicons' → 'App\Lexicons').
    |
    */

    'generators' => [
        // Output directory for generated Lexicon classes
        'lexicon_path' => 'app/Lexicons',
    ],

    /*
    |--------------------------------------------------------------------------
    | Bundled Pre-Generated Classes
    |--------------------------------------------------------------------------
    |
    | The package includes pre-generated PHP classes for all standard
    | AT Protocol and Bluesky lexicons in the SocialDept\AtpSchema\Generated
    | namespace. These provide immediate type-safe access to records.
    |
    */

    'generated' => [
        // Namespace for bundled pre-generated classes
        'namespace' => 'SocialDept\\AtpSchema\\Generated',

        // Enable usage of bundled generated classes
        'enabled' => env('SCHEMA_USE_GENERATED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Caching Configuration
    |--------------------------------------------------------------------------
    |
    | Configure caching for parsed schemas and resolved lexicons.
    | TTL values are in seconds.
    |
    */

    'cache' => [
        // Enable or disable schema caching
        'enabled' => env('SCHEMA_CACHE_ENABLED', true),

        // Cache driver to use (inherits from config/cache.php if null)
        'driver' => env('SCHEMA_CACHE_DRIVER', null),

        // Cache TTL for parsed schemas (1 hour)
        'schema_ttl' => env('SCHEMA_CACHE_TTL', 3600),

        // Cache TTL for DNS resolution results (24 hours)
        'dns_ttl' => env('SCHEMA_DNS_CACHE_TTL', 86400),

        // Cache key prefix
        'prefix' => env('SCHEMA_CACHE_PREFIX', 'schema'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Settings
    |--------------------------------------------------------------------------
    |
    | Configure validation behavior for records against Lexicon schemas.
    |
    */

    'validation' => [
        // Default validation mode: strict, optimistic, lenient
        'mode' => env('SCHEMA_VALIDATION_MODE', 'strict'),

        // Validate on Data class construction
        'validate_on_construct' => env('SCHEMA_VALIDATE_ON_CONSTRUCT', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Blob Handling
    |--------------------------------------------------------------------------
    |
    | Configure blob storage and handling for ATProto blob references.
    |
    */

    'blobs' => [
        // Storage disk for blobs
        'disk' => env('SCHEMA_BLOB_DISK', 'local'),

        // Storage path prefix for blobs
        'path' => env('SCHEMA_BLOB_PATH', 'blobs'),

        // Lazy load blob content (don't download until accessed)
        'lazy_load' => env('SCHEMA_BLOB_LAZY_LOAD', true),

        // Blob URL signing (for temporary access URLs)
        'signed_urls' => env('SCHEMA_BLOB_SIGNED_URLS', true),

        // Signed URL expiration (in minutes)
        'signed_url_expiration' => env('SCHEMA_BLOB_URL_EXPIRATION', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | DNS-Based Lexicon Resolution
    |--------------------------------------------------------------------------
    |
    | Configure DNS-based lexicon resolution following AT Protocol specification.
    |
    | When enabled, the schema loader will attempt to discover custom lexicons via:
    | 1. Querying DNS TXT record at _lexicon.<authority-domain> for DID
    | 2. Resolving DID to PDS endpoint (requires socialdept/atp-resolver)
    | 3. Fetching lexicon from repository via com.atproto.repo.getRecord
    |
    | IMPORTANT: DNS resolution requires the optional socialdept/atp-resolver package.
    | Install with: composer require socialdept/atp-resolver
    |
    | If atp-resolver is not installed, DNS resolution will be skipped and a
    | warning will be logged. The schema loader will fall back to local sources.
    |
    */

    'dns_resolution' => [
        // Enable DNS-based lexicon resolution (requires socialdept/atp-resolver)
        'enabled' => env('SCHEMA_DNS_RESOLUTION_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Client Configuration
    |--------------------------------------------------------------------------
    |
    | Configure HTTP client used for schema retrieval via XRPC.
    |
    */

    'http' => [
        // Request timeout (seconds)
        'timeout' => env('SCHEMA_HTTP_TIMEOUT', 10),

        // Connection timeout (seconds)
        'connect_timeout' => env('SCHEMA_HTTP_CONNECT_TIMEOUT', 5),

        // User agent for HTTP requests
        'user_agent' => env('SCHEMA_HTTP_USER_AGENT', 'SocialDept/Schema'),
    ],

];
