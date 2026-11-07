# Extending

Part of the [atp-schema docs](introduction.md#documentation). Hooks and macros for adding your own behavior.

## Macros

`ModelMapper`, `UnionResolver`, `BlobHandler` and the standalone `Validator` are macroable, so you can add methods of your own:

```php
use SocialDept\AtpSchema\Services\ModelMapper;

ModelMapper::macro('types', function (): array {
    return array_keys($this->all());
});

$mapper = new ModelMapper();
$mapper->register('app.bsky.feed.post', new PostTransformer());

$mapper->types(); // ['app.bsky.feed.post']
```

## Hooks

`ExtensionManager` keeps named callbacks and runs them:

```php
use SocialDept\AtpSchema\Support\ExtensionManager;

$extensions = new ExtensionManager();

$extensions->hook('before:save', function (array $data) {
    $data['text'] = trim($data['text']);

    return $data;
});

$data = $extensions->filter('before:save', $data);    // passes the value through each callback
$results = $extensions->execute('before:save', $data); // every callback's result
$first = $extensions->executeUntil('before:save', $data); // the first result that is not null
```

It also has `has()`, `get()`, `remove()`, `clear()`, `count()` and `names()`.

### Generator hooks

The class generator runs its output through hooks, so you can change the code it writes. It keeps its own `ExtensionManager`, reached with `getExtensions()`:

```php
use SocialDept\AtpSchema\Data\LexiconDocument;
use SocialDept\AtpSchema\Generator\ClassGenerator;
use SocialDept\AtpSchema\Generator\NamingConverter;

$classes = new ClassGenerator(new NamingConverter('App\\Lexicons'));

$classes->getExtensions()->hook('filter:class:docBlock', function (string $docBlock, LexiconDocument $document) {
    return $docBlock;
});

$code = $classes->generate($document);
```

| Hook | Receives |
|---|---|
| `filter:class:namespace` | The namespace and the document |
| `filter:class:className` | The class name and the document |
| `filter:class:useStatements` | The imports, the document and the definition |
| `filter:class:properties` | The properties, the document and the definition |
| `filter:class:constructor` | The constructor, the document and the definition |
| `filter:class:methods` | The methods and the document |
| `filter:class:docBlock` | The class docblock, the document and the definition |
| `action:class:generated` | The finished code and the document |

To use it with `schema:generate`'s output settings, pass it to `DTOGenerator` as `classGenerator`. `TypeMapper` and `MethodGenerator` have hooks of their own (`filter:type:*` and `filter:method:*`) on their own managers.
