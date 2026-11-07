# Configuration

Part of the [atp-schema docs](../introduction.md#documentation). Every setting the package reads, with its default and env var.

The settings live in `config/atp-schema.php`. Publish it with `php artisan vendor:publish --tag=atp-schema-config` to change them.

## Lexicon sources

```php
'sources' => [
    resource_path('lexicons'),
    __DIR__.'/../resources/lexicons',
],
```

The directories searched for lexicons, in order. The first is your app's `resources/lexicons`, the second is the set bundled with the package. A lexicon `com.example.thing` is read from `com/example/thing.json` under a source. When no source has it, the loader tries the network (see [DNS resolution](#dns-resolution)).

> [!WARNING]
> In a published config, `__DIR__` is your app's `config` directory, so the second entry points at your own `resources/lexicons` instead of the bundled set. To keep the bundled lexicons, point it at `base_path('vendor/socialdept/atp-schema/resources/lexicons')`, or publish them with `--tag=atp-lexicons`.

## Authoring

| Key | Default | Used by |
|---|---|---|
| `lexicon_path` | `resource_path('lexicons')` | Where `schema:new` writes lexicons and `schema:pull` copies them. The other authoring commands read from here. |
| `lock_file` | `base_path('lexicons.lock.json')` | The lock file `schema:pull` keeps of what it pulled. |

Commit both. See [Authoring commands](../authoring/commands.md).

## Generator

| Key | Default | Used by |
|---|---|---|
| `generators.lexicon_path` | `app/Lexicons` | Where `schema:generate` writes classes. The namespace comes from the path, so `app/Lexicons` becomes `App\Lexicons`. |

See [Generating classes](../lexicons/generating.md).

## Cache

| Key | Env | Default |
|---|---|---|
| `cache.enabled` | `SCHEMA_CACHE_ENABLED` | `true` |
| `cache.schema_ttl` | `SCHEMA_CACHE_TTL` | `3600` |
| `cache.prefix` | `SCHEMA_CACHE_PREFIX` | `schema` |

Loaded lexicons are kept in Laravel's cache for `schema_ttl` seconds, under `{prefix}:parsed:{nsid}`, and in memory for the rest of the request. Clear them with `schema:clear-cache` (see [Loading lexicons](../lexicons/loading.md#clearing-the-cache)).

## DNS resolution

| Key | Env | Default |
|---|---|---|
| `dns_resolution.enabled` | `SCHEMA_DNS_RESOLUTION_ENABLED` | `true` |
| `http.timeout` | `SCHEMA_HTTP_TIMEOUT` | `10` |

When a lexicon is in none of the sources, the loader looks up the `_lexicon` TXT record of its authority and fetches the schema from the repo the record names, through [atp-support](https://github.com/socialdept/atp-support). `http.timeout` is in seconds, and the authoring commands use it for their network reads too. See [Publishing and resolution](../authoring/publishing.md).
