# Blobs

Part of the [atp-schema docs](../introduction.md#documentation). Storing uploads as blob references, with size and MIME type limits.

`BlobHandler` stores a file on a Laravel disk and returns a `BlobReference` to put in a record:

```php
use SocialDept\AtpSchema\Services\BlobHandler;

$blobs = new BlobHandler('local');   // the disk, and an optional path prefix, 'blobs' by default

$blob = $blobs->store(request()->file('image'), [
    'accept' => ['image/*'],
    'maxSize' => 1024 * 1024 * 5,    // bytes
]);

$record = [
    'image' => $blob->toArray(),     // ['$type' => 'blob', 'ref' => ['$link' => ...], 'mimeType' => ..., 'size' => ...]
];
```

The limits are optional. `accept` takes MIME types with wildcards, and `minSize` works like `maxSize`. A file that breaks one throws a `RecordValidationException`. With no disk, the handler uses the app's default filesystem disk.

> [!IMPORTANT]
> The `ref` the handler gives a blob is built from a SHA-256 hash of the content. It is not a real CID, so it does not match the CID a PDS gives the same blob.

## The handler

```php
$blobs->store(UploadedFile $file, ?array $constraints = null): BlobReference
$blobs->storeFromString(string $content, string $mimeType, ?array $constraints = null): BlobReference
$blobs->get(string $cid): ?string
$blobs->exists(string $cid): bool
$blobs->size(string $cid): ?int
$blobs->delete(string $cid): bool
$blobs->validate(BlobReference $blob, array $constraints): void
```

## Blob references

```php
use SocialDept\AtpSchema\Data\BlobReference;

$blob = BlobReference::fromArray($record['image']);   // takes 'ref' as a string or as ['$link' => ...]

$blob->getCid();
$blob->getMimeType();
$blob->getSize();
$blob->isImage();
$blob->isVideo();
$blob->matchesMimeType('image/*');
```
