# Validating records

Part of the [atp-schema docs](../introduction.md#documentation). Checking records, queries and procedures against their lexicons, from code or the command line.

## With the facade

The facade loads the lexicon by NSID (see [Loading lexicons](../lexicons/loading.md)) and checks a record against it:

```php
use SocialDept\AtpSchema\Facades\Schema;

$record = [
    'text' => 'Hello, AT Protocol!',
    'createdAt' => '2024-01-01T12:00:00Z',
];

Schema::validate('app.bsky.feed.post', $record);            // true

Schema::validateWithErrors('app.bsky.feed.post', ['text' => 'Hi', 'createdAt' => 'yesterday']);
// ['record' => ["Invalid value at '$.createdAt': must be a valid ISO 8601 datetime"]]
```

This checks the record's fields, nested objects, arrays and [string formats](types-and-formats.md). It stops at the first problem and reports it under `record`, with the path to the field. A problem with the lexicon itself, such as one that is not a record, is reported under `schema`. Fields the lexicon does not define are allowed, so records written by newer clients still pass.

> [!NOTE]
> A field that points at another definition with a `ref` is not checked against that definition yet, and a union value is checked only for its `$type` (see [Unions](unions.md)).

## Queries and procedures

The validator behind the facade also checks a query's parameters and a procedure's JSON input. These throw a `RecordValidationException` instead of returning a result:

```php
use SocialDept\AtpSchema\Facades\Schema;
use SocialDept\AtpSchema\Validation\LexiconValidator;

$validator = app(LexiconValidator::class);

$validator->validateQuery(Schema::load('app.bsky.feed.getTimeline'), ['limit' => 50]);
$validator->validateProcedure(Schema::load('com.atproto.repo.createRecord'), $input);
$validator->validateByNsid('app.bsky.feed.post', $record);
```

A procedure whose input is not `application/json`, such as a blob upload, has nothing to check and passes.

## From the command line

```bash
php artisan schema:validate app.bsky.feed.post --data='{"text":"Hi","createdAt":"2024-01-01T12:00:00Z"}'
php artisan schema:validate app.bsky.feed.post --file=post.json
```

The command exits with a failure and lists the errors when the record does not pass.

## The standalone validator

`SocialDept\AtpSchema\Validation\Validator` is a lighter validator for a lexicon document you build or load yourself. It collects every error, keyed by field, and has three modes:

```php
use SocialDept\AtpSchema\Data\LexiconDocument;
use SocialDept\AtpSchema\Parser\SchemaLoader;
use SocialDept\AtpSchema\Validation\Validator;

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

$validator = new Validator(new SchemaLoader([]));

$validator->validate(['text' => 'Hello', 'createdAt' => '2024-01-01T12:00:00Z'], $schema); // true

$validator->validateWithErrors(['text' => str_repeat('a', 301), 'createdAt' => '2024-01-01T12:00:00Z'], $schema);
// ['text' => ['String exceeds maximum length of 300']]
```

| Mode | Unknown fields | Required fields | Length, range, enum and const limits |
|---|---|---|---|
| `Validator::MODE_STRICT` (the default) | Rejected | Checked | Checked |
| `Validator::MODE_OPTIMISTIC` | Allowed | Checked | Checked |
| `Validator::MODE_LENIENT` | Allowed | Not checked | Not checked |

```php
$validator->setMode(Validator::MODE_OPTIMISTIC);
$validator->getMode(); // 'optimistic'
```

> [!NOTE]
> The standalone validator checks types, lengths, ranges, enums, nested objects and arrays, but not string formats. Use the facade when formats matter.
