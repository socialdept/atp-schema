# Introduction

**atp-schema** is a Laravel package for working with AT Protocol lexicons:

- **Loading:** find lexicons in your app, in the set bundled with the package, or on the network through DNS.
- **Validation:** check records against their lexicon, including string formats such as `datetime`, `did` and `at-uri`, and use the same formats as Laravel validation rules.
- **Bundled classes:** PHP classes and enums for the standard AT Protocol and Bluesky lexicons, ready to use with no generation step.
- **Generation:** turn any lexicon into PHP classes with `schema:generate`, without overwriting the ones you changed.
- **Blobs and unions:** store uploads as blob references, and check discriminated unions by their `$type`.
- **Authoring:** create, lint and diff your own lexicons, find breaking changes, and pull other people's lexicons into the app with a lock file.

## A first look

Validate a record against its lexicon:

```php
use SocialDept\AtpSchema\Facades\Schema;

Schema::validate('app.bsky.feed.post', [
    'text' => 'Hello, AT Protocol!',
    'createdAt' => '2024-01-01T12:00:00Z',
]); // true
```

Use a bundled class to build a record:

```php
use SocialDept\AtpSchema\Generated\App\Bsky\Feed\Post;

$post = new Post(text: 'Hello ATP!', createdAt: now());

$post->toRecord(); // the record as an array, with its $type
```

Check your own lexicons against the versions you already published:

```bash
php artisan schema:breaking
```

## Documentation

Start with [Installation](getting-started/installation.md), then [Loading lexicons](lexicons/loading.md) and [Validating records](validation/validating.md). The docs are grouped by area, and each group has an index of its own:

- **[Getting started](getting-started/README.md)**: [Installation](getting-started/installation.md), [Configuration](getting-started/configuration.md)
- **[Lexicons](lexicons/README.md)**: [Loading lexicons](lexicons/loading.md), [Bundled classes](lexicons/bundled-classes.md), [Generating classes](lexicons/generating.md)
- **[Validation](validation/README.md)**: [Validating records](validation/validating.md), [Types and formats](validation/types-and-formats.md), [Unions](validation/unions.md), [Blobs](validation/blobs.md)
- **[Authoring](authoring/README.md)**: [Authoring commands](authoring/commands.md), [Breaking changes](authoring/breaking-changes.md), [Publishing and resolution](authoring/publishing.md)
- **[Model mapping](model-mapping.md)**: Turning records into your own objects and back with transformers.
- **[Extending](extending.md)**: Hooks and macros for adding your own behavior.
