# Generating classes

Part of the [atp-schema docs](../introduction.md#documentation). Turning any lexicon into PHP classes, and keeping the ones you changed.

## The command

```bash
# One lexicon
php artisan schema:generate app.bsky.feed.post

# With every lexicon it references, recursively (-r for short)
php artisan schema:generate app.bsky.feed.post --with-dependencies

# Replace classes generated before
php artisan schema:generate app.bsky.feed.post --force

# Print the code without writing anything
php artisan schema:generate app.bsky.feed.post --dry-run

# Another directory and namespace
php artisan schema:generate app.bsky.feed.post --output=app/DTOs --namespace="App\DTOs"
```

Classes are written to `app/Lexicons` by default, in the `App\Lexicons` namespace. Change the directory with `generators.lexicon_path` in `config/atp-schema.php`, and the namespace follows it. The lexicon must be one the loader can find (see [Loading lexicons](loading.md)). Without `--force`, a class that already exists stops the command with an error.

## Keeping your changes

Generated classes carry a `#[Generated]` attribute:

```php
use SocialDept\AtpSchema\Attributes\Generated;

#[Generated(regenerate: true)]
class Post extends Data
{
    // ...
}
```

With `--force`, only files that still have `#[Generated]` with `regenerate: true` are replaced. To keep a class you changed, set `regenerate: false` or remove the attribute, and the command skips it and lists it as skipped.
