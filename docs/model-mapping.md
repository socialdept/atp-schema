# Model mapping

Part of the [atp-schema docs](introduction.md#documentation). Turning records into your own objects and back with transformers.

A transformer converts one lexicon's records to a class of yours and back. It implements `SocialDept\AtpSchema\Contracts\Transformer`:

```php
use SocialDept\AtpSchema\Contracts\Transformer;

class PostTransformer implements Transformer
{
    public function fromArray(array $data): Post
    {
        return new Post(
            text: $data['text'],
            createdAt: $data['createdAt'],
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
```

Register it with a `ModelMapper` under the NSID, then convert by NSID:

```php
use SocialDept\AtpSchema\Services\ModelMapper;

$mapper = new ModelMapper();
$mapper->register('app.bsky.feed.post', new PostTransformer());

$post = $mapper->fromArray('app.bsky.feed.post', $data);
$array = $mapper->toArray('app.bsky.feed.post', $post);
```

`fromArray()` and `toArray()` throw a `SchemaException` when no transformer is registered for the type. The `try` versions return `null` instead.

## The mapper

```php
$mapper->register(string $type, Transformer $transformer): self
$mapper->registerMany(array $transformers): self      // [type => transformer]
$mapper->fromArray(string $type, array $data): mixed
$mapper->toArray(string $type, mixed $model): array
$mapper->fromArrayMany(string $type, array $items): array
$mapper->toArrayMany(string $type, array $items): array
$mapper->tryFromArray(string $type, array $data): mixed
$mapper->tryToArray(string $type, mixed $model): ?array
$mapper->getTransformer(string $type): ?Transformer
$mapper->has(string $type): bool
$mapper->unregister(string $type): self
$mapper->all(): array
$mapper->clear(): self
$mapper->count(): int
```
