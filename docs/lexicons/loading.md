# Loading lexicons

Part of the [atp-schema docs](../introduction.md#documentation). Finding lexicons by NSID, the facade and helpers, listing, and the cache.

## Where lexicons come from

A lexicon is looked up by its NSID in each directory of `atp-schema.sources`, in order. `com.example.thing` is read from `com/example/thing.json`. By default that is your app's `resources/lexicons`, then the set bundled with the package. When no source has it and DNS resolution is on, the loader resolves it from the network, the way [Publishing and resolution](../authoring/publishing.md) describes. A lexicon found nowhere throws `SchemaNotFoundException`.

## The facade

```php
use SocialDept\AtpSchema\Facades\Schema;

$document = Schema::load('app.bsky.feed.post');   // LexiconDocument, or throws
$document = Schema::find('com.example.missing');  // LexiconDocument or null
Schema::exists('app.bsky.feed.post');             // bool
Schema::all();                                     // every NSID in the source directories
Schema::clearCache('app.bsky.feed.post');          // drop one lexicon from the cache
```

The same calls are available as helpers:

```php
schema();                        // the SchemaManager
schema('app.bsky.feed.post');    // load a lexicon
schema_find('app.bsky.feed.post');
schema_exists('app.bsky.feed.post');
schema_validate('app.bsky.feed.post', $data);
```

## Lexicon documents

A loaded lexicon is a `LexiconDocument`. You can also build one yourself:

```php
use SocialDept\AtpSchema\Data\LexiconDocument;

$document = LexiconDocument::fromJson(file_get_contents($path));
$document = LexiconDocument::fromArray($array);

$document->getNsid();            // 'app.bsky.feed.post'
$document->getVersion();         // 1
$document->getMainDefinition();  // the 'main' definition, or null
$document->getDefinition('replyRef');
$document->getDefinitionNames();
$document->isRecord();           // also isQuery(), isProcedure(), isSubscription()
```

## Listing lexicons

```bash
php artisan schema:list
php artisan schema:list --type=record
```

`--type` is one of `record`, `query`, `procedure` or `subscription`.

## Clearing the cache

Loaded lexicons are cached (see [Configuration](../getting-started/configuration.md#cache)). Clear one, or all of them:

```bash
php artisan schema:clear-cache --nsid=app.bsky.feed.post
php artisan schema:clear-cache --all
```

> [!WARNING]
> On a cache store without tags, such as `file` or `database`, `--all` calls `Cache::flush()` and empties the whole application cache, not only the lexicons.
