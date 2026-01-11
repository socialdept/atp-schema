[![Schema Header](./header.png)](https://github.com/socialdept/atp-signals)

<h3 align="center">
    Parse, validate, and transform AT Protocol Lexicon schemas in Laravel.
</h3>

<p align="center">
    <br>
    <a href="https://packagist.org/packages/socialdept/atp-schema" title="Latest Version on Packagist"><img src="https://img.shields.io/packagist/v/socialdept/atp-schema.svg?style=flat-square"></a>
    <a href="https://packagist.org/packages/socialdept/atp-schema" title="Total Downloads"><img src="https://img.shields.io/packagist/dt/socialdept/atp-schema.svg?style=flat-square"></a>
    <a href="LICENSE" title="Software License"><img src="https://img.shields.io/github/license/socialdept/atp-schema?style=flat-square"></a>
</p>

---

## What is Schema?

**Schema** is a Laravel package for working with AT Protocol Lexicon schemas. It provides comprehensive tools for parsing schema definitions, validating data against those schemas, handling file uploads (blobs), and transforming between raw data and domain models.

Think of it as your complete toolkit for building AT Protocol applications that need strict data validation and schema compliance.

## Why use Schema?

- **Complete Lexicon support** - All types, formats, and constraints from the AT Protocol spec
- **Multiple validation modes** - STRICT, OPTIMISTIC, and LENIENT for different use cases
- **Laravel integration** - Works seamlessly with Laravel's validation and storage systems
- **Blob handling** - Built-in file upload validation and storage
- **Model transformation** - Convert between arrays and domain objects
- **Union types** - Full support for discriminated unions with `$type` fields
- **Extensible** - Macros and hooks let you customize behavior
- **Production ready** - 842 passing tests with comprehensive coverage
- **Pre-generated classes** - Includes type-safe PHP classes for all standard AT Protocol & Bluesky lexicons

## Pre-Generated Lexicon Classes

Schema ships with pre-generated PHP classes for all standard AT Protocol and Bluesky lexicons, providing immediate type-safe access without any generation step:

```php
use SocialDept\AtpSchema\Generated\App\Bsky\Feed\Post;
use SocialDept\AtpSchema\Generated\App\Bsky\Graph\Follow;
use SocialDept\AtpSchema\Generated\Com\Atproto\Repo\StrongRef;

// Create type-safe records
$post = new Post(
    text: 'Hello ATP!',
    createdAt: now()
);

// Create references
$ref = new StrongRef(
    uri: 'at://did:plc:example/app.bsky.feed.post/123',
    cid: 'bafyreic3...'
);

// Validate and use
Schema::validate('app.bsky.feed.post', $post); // true
```

### Available Pre-Generated Classes

Schema includes **220+ pre-generated classes** covering all standard AT Protocol and Bluesky lexicons:

**Record Types (`SocialDept\AtpSchema\Generated\App\Bsky\*`)**
- `Feed\Post` - Social media posts
- `Feed\Like` - Like records
- `Feed\Repost` - Repost records
- `Graph\Follow` - Follow relationships
- `Graph\Block` - Block records
- `Graph\List` - User lists
- `Graph\Listitem` - List items
- `Labeler\Service` - Labeler service records

**Embed Types**
- `Embed\Images` - Image embeds
- `Embed\External` - External link embeds
- `Embed\Record` - Record embeds
- `Embed\RecordWithMedia` - Record with media embeds
- `Embed\Video` - Video embeds

**Feed & Post Views (`App\Bsky\Feed\Defs\*`)**
- `PostView` - Post with engagement metrics
- `FeedViewPost` - Post in feed context
- `ThreadViewPost` - Post in thread context
- `ReplyRef` - Reply references
- `ViewerState` - User's interaction state

**Actor & Profile Views (`App\Bsky\Actor\Defs\*`)**
- `ProfileView` - Full profile view
- `ProfileViewBasic` - Basic profile view
- `ProfileViewDetailed` - Detailed profile view
- `ViewerState` - Viewer's relationship to profile
- Plus 25+ preference and state classes

**Graph & Social Views (`App\Bsky\Graph\Defs\*`)**
- `ListView` - List views
- `ListItemView` - List item views
- `Relationship` - User relationships
- `StarterPackView` - Starter pack views

**Rich Text (`App\Bsky\Richtext\Facet\*`)**
- `Facet` - Text annotations (mentions, URLs, hashtags)
- `Mention` - User mention facets
- `Link` - URL link facets
- `Tag` - Hashtag facets

**AT Protocol Core (`Com\Atproto\*`)**
- `Repo\StrongRef` - Content-addressed record references
- `Repo\Defs\CommitMeta` - Repository commit metadata
- `Label\Defs\Label` - Content labels
- `Admin\Defs\*` - Administrative tools (20+ classes)
- `Sync\SubscribeRepos\*` - Repository sync events (6+ classes)
- `Server\Defs\*` - Server definitions

**Moderation (`Tools\Ozone\*`)**
- `Moderation\Defs\*` - Moderation definitions
- `Communication\Defs\*` - Moderation communication

### Generated Enums

Schema also generates PHP 8.1+ enums for string types with known values:

**Moderation**
- `Com\Atproto\Moderation\ReasonType` - Report reason types (spam, violation, etc.)
- `Com\Atproto\Moderation\SubjectType` - Report subject types (account, record, chat)

**Labels & Content**
- `Com\Atproto\Label\LabelValue` - Content label values

**Graph & Social**
- `App\Bsky\Graph\ListPurpose` - List purpose types (modlist, curatelist)
- `App\Bsky\Actor\MutedWordTarget` - Muted word target types

**Moderation Tools**
- `Tools\Ozone\Moderation\SubjectReviewState` - Review state values

### Publishing Source Lexicons

Developers can optionally publish the source JSON lexicons to their project for reference or custom generation:

```bash
php artisan vendor:publish --tag=atp-lexicons
```

This copies all lexicon JSON files to `resources/lexicons/`.

### Generating Custom DTOs

Generate PHP classes from any lexicon schema:

```bash
# Generate a single lexicon
php artisan schema:generate app.bsky.feed.post

# Generate with all dependencies
php artisan schema:generate app.bsky.feed.post --with-dependencies

# Regenerate existing files
php artisan schema:generate app.bsky.feed.post --force
```

Generated classes include a `#[Generated]` attribute that controls regeneration behavior:

```php
use SocialDept\AtpSchema\Attributes\Generated;

#[Generated(regenerate: true)]  // Will be overwritten on next --force
class Post extends Data
{
    // ...
}
```

**Protecting customized files:** If you modify a generated class and want to prevent it from being overwritten:

1. Change `regenerate: true` to `regenerate: false`, or
2. Remove the `#[Generated]` attribute entirely

Files without the attribute or with `regenerate: false` will be skipped when running `--force`, keeping your customizations safe.

## Quick Example

```php
use SocialDept\AtpSchema\Data\LexiconDocument;
use SocialDept\AtpSchema\Validation\Validator;
use SocialDept\AtpSchema\Parser\SchemaLoader;

// Load a schema
$schema = LexiconDocument::fromArray([
    'lexicon' => 1,
    'id' => 'app.bsky.feed.post',
    'defs' => [
        'main' => [
            'type' => 'record',
            'record' => [
                'type' => 'object',
                'required' => ['text', 'createdAt'],
                'properties' => [
                    'text' => ['type' => 'string', 'maxLength' => 300],
                    'createdAt' => ['type' => 'string', 'format' => 'datetime'],
                ],
            ],
        ],
    ],
]);

// Validate data
$validator = new Validator(new SchemaLoader([]));

$data = [
    'text' => 'Hello, AT Protocol!',
    'createdAt' => '2024-01-01T12:00:00Z',
];

if ($validator->validate($data, $schema)) {
    // Data is valid!
}

// Get Laravel-formatted errors
$errors = $validator->validateWithErrors($invalidData, $schema);
// ['text' => ['The text field exceeds maximum length.']]
```

## Installation

```bash
composer require socialdept/atp-schema
```

The package will auto-register with Laravel. Optionally publish the config:

```bash
php artisan vendor:publish --tag=schema-config
```

## Basic Usage

### Validation Modes

Choose the validation strictness that fits your use case:

```php
use SocialDept\AtpSchema\Validation\Validator;

// STRICT - Rejects unknown fields
$validator->setMode(Validator::MODE_STRICT);

// OPTIMISTIC - Allows unknown fields (default)
$validator->setMode(Validator::MODE_OPTIMISTIC);

// LENIENT - Skips constraint validation
$validator->setMode(Validator::MODE_LENIENT);
```

### Handling Blobs

Upload and validate files with built-in constraints:

```php
use SocialDept\AtpSchema\Services\BlobHandler;

$blobHandler = new BlobHandler('local');

$blob = $blobHandler->store(request()->file('image'), [
    'accept' => ['image/*'],
    'maxSize' => 1024 * 1024 * 5, // 5MB
]);

// Use in validated data
$data = [
    'image' => $blob->toArray(),
];
```

### Model Transformation

Transform between raw arrays and domain objects:

```php
use SocialDept\AtpSchema\Services\ModelMapper;
use SocialDept\AtpSchema\Contracts\Transformer;

class Post
{
    public function __construct(
        public string $text,
        public string $createdAt
    ) {}
}

class PostTransformer implements Transformer
{
    public function fromArray(array $data): Post
    {
        return new Post(
            text: $data['text'],
            createdAt: $data['createdAt']
        );
    }

    public function toArray(mixed $model): array
    {
        return [
            'text' => $model->text,
            'createdAt' => $model->createdAt,
        ];
    }

    public function supports(string $type): bool
    {
        return $type === 'app.bsky.feed.post';
    }
}

// Register and use
$mapper = new ModelMapper();
$mapper->register('app.bsky.feed.post', new PostTransformer());

$post = $mapper->fromArray('app.bsky.feed.post', $data);
$array = $mapper->toArray('app.bsky.feed.post', $post);
```

### Union Types

Work with discriminated unions using the `$type` field:

```php
use SocialDept\AtpSchema\Services\UnionResolver;

$resolver = new UnionResolver();

$data = [
    '$type' => 'app.bsky.embed.images',
    'images' => [/* ... */],
];

$type = $resolver->extractType($data);
// "app.bsky.embed.images"

// Validate discriminated union
$refs = ['app.bsky.embed.images', 'app.bsky.embed.video'];
$resolver->validateDiscriminated($data, $refs);
```

## Complete Workflow Example

Here's how to validate a post with an image upload:

```php
use SocialDept\AtpSchema\Data\LexiconDocument;
use SocialDept\AtpSchema\Validation\Validator;
use SocialDept\AtpSchema\Services\BlobHandler;

// Load schema
$schema = LexiconDocument::fromArray([/* ... */]);

// Handle image upload
$blobHandler = new BlobHandler('local');
$blob = $blobHandler->store(request()->file('image'), [
    'accept' => ['image/*'],
    'maxSize' => 1024 * 1024 * 5,
]);

// Create post data
$postData = [
    'text' => 'Check out this photo!',
    'createdAt' => now()->toIso8601String(),
    'embed' => [
        '$type' => 'app.bsky.embed.images',
        'images' => [
            [
                'image' => $blob->toArray(),
                'alt' => 'A beautiful sunset',
            ],
        ],
    ],
];

// Validate
$validator = new Validator(new SchemaLoader([]));

if ($validator->validate($postData, $schema)) {
    // Store post...
} else {
    $errors = $validator->validateWithErrors($postData, $schema);
    // Handle errors...
}
```

## Supported Types

Schema supports all AT Protocol Lexicon types:

- **Primitives** - `string`, `integer`, `boolean`, `bytes`
- **Objects** - Nested objects with properties
- **Arrays** - Sequential lists with item validation
- **Blobs** - File uploads with mime type and size constraints
- **Unions** - Discriminated unions with `$type` field
- **Unknown** - Accept any value

## Supported Formats

Built-in validators for AT Protocol formats:

- `datetime` - ISO 8601 timestamps
- `uri` - Valid URIs
- `at-uri` - AT Protocol URIs
- `did` - Decentralized identifiers
- `nsid` - Namespaced identifiers
- `cid` - Content identifiers

## Advanced Features

### Extension Hooks

Add custom logic at key points in the validation lifecycle:

```php
use SocialDept\AtpSchema\Support\ExtensionManager;

$extensions = new ExtensionManager();

$extensions->hook('before:validate', function ($data) {
    $data['text'] = trim($data['text']);
    return $data;
});

$transformed = $extensions->filter('before:validate', $data);
```

### Macros

Extend core services with custom methods:

```php
use SocialDept\AtpSchema\Services\ModelMapper;

ModelMapper::macro('validateAndTransform', function ($type, $data, $schema) {
    if (!$this->validator->validate($data, $schema)) {
        return null;
    }
    return $this->fromArray($type, $data);
});

$mapper = new ModelMapper();
$result = $mapper->validateAndTransform('app.bsky.feed.post', $data, $schema);
```

## API Reference

### Core Classes

**LexiconDocument**
```php
LexiconDocument::fromArray(array $data): self
LexiconDocument::fromJson(string $json): self
$document->getNsid(): string
$document->getVersion(): int
$document->getDefinition(string $name): ?array
$document->getMainDefinition(): ?array
```

**Validator**
```php
$validator->validate(array $data, LexiconDocument $schema): bool
$validator->validateWithErrors(array $data, LexiconDocument $schema): array
$validator->setMode(string $mode): self
```

**BlobHandler**
```php
$handler->store(UploadedFile $file, array $constraints = []): BlobReference
$handler->storeFromString(string $content, string $mimeType): BlobReference
$handler->get(string $ref): ?string
$handler->delete(string $ref): bool
$handler->exists(string $ref): bool
```

**ModelMapper**
```php
$mapper->register(string $type, Transformer $transformer): self
$mapper->fromArray(string $type, array $data): mixed
$mapper->toArray(string $type, mixed $model): array
$mapper->fromArrayMany(string $type, array $items): array
```

**UnionResolver**
```php
$resolver->extractType(array $data): ?string
$resolver->validateDiscriminated(mixed $data, array $refs): void
$resolver->getTypeDefinition(array $data, array $definition): ?LexiconDocument
```

## Testing

Run the test suite:

```bash
vendor/bin/phpunit
```

Run specific test categories:

```bash
# Unit tests only
vendor/bin/phpunit --testsuite=unit

# Integration tests only
vendor/bin/phpunit --testsuite=integration
```

## Configuration

Customize behavior in `config/schema.php`:

```php
return [
    'storage' => [
        'disk' => env('SCHEMA_STORAGE_DISK', 'local'),
    ],

    'validation' => [
        'mode' => env('SCHEMA_VALIDATION_MODE', 'optimistic'),
    ],

    'blob' => [
        'max_size' => env('SCHEMA_BLOB_MAX_SIZE', 1024 * 1024 * 10), // 10MB
    ],
];
```

## Requirements

- PHP 8.2+
- Laravel 11+

## Resources

- [AT Protocol Documentation](https://atproto.com/)
- [Lexicon Specification](https://atproto.com/specs/lexicon)
- [Bluesky API Docs](https://docs.bsky.app/)

## Support & Contributing

Found a bug or have a feature request? [Open an issue](https://github.com/socialdept/atp-schema/issues).

Want to contribute? We'd love your help! Check out the [contribution guidelines](CONTRIBUTING.md).

## Credits

- [Miguel Batres](https://batres.co) - founder & lead maintainer
- [All contributors](https://github.com/socialdept/atp-schema/graphs/contributors)

## License

Schema is open-source software licensed under the [MIT license](LICENSE).

---

**Built for the Federation** • By Social Dept.
