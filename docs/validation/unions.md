# Unions

Part of the [atp-schema docs](../introduction.md#documentation). Reading and checking discriminated unions by their `$type`.

A union field holds one of several types, and the value names its own type in `$type`. A closed union allows only the types it lists, an open one allows others too. Record validation checks that a union value has a `$type`, and for a closed union that the type is one it lists.

`UnionResolver` does the same by hand:

```php
use SocialDept\AtpSchema\Services\UnionResolver;

$resolver = new UnionResolver();

$embed = [
    '$type' => 'app.bsky.embed.images',
    'images' => [/* ... */],
];

$resolver->extractType($embed); // 'app.bsky.embed.images'

// Throws a RecordValidationException when $type is missing or not in the list
$resolver->validateDiscriminated($embed, ['app.bsky.embed.images', 'app.bsky.embed.video']);
```

Given the union's definition from the lexicon:

```php
$union = ['type' => 'union', 'refs' => ['app.bsky.embed.images', 'app.bsky.embed.video'], 'closed' => true];

$resolver->resolve($embed, $union);       // the $type for a closed union, null for an open one
$resolver->matches($embed, 'app.bsky.embed.images', $union); // true
$resolver->isClosed($union);              // true
$resolver->getTypes($union);              // the refs

$resolver->createDiscriminated('app.bsky.embed.images', ['images' => []]); // adds '$type'
```

`getTypeDefinition($data, $union)` returns the lexicon of the resolved type, when the resolver was given a `LexiconRegistry` with `setRegistry()`.
