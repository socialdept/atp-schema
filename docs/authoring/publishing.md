# Publishing and resolution

Part of the [atp-schema docs](../introduction.md#documentation). How other people find your lexicons, pulling theirs into your app, and the lock file.

## How resolution works

An NSID's authority publishes a TXT record at `_lexicon.<authority domain>` holding `did=<DID>`, and the schema as a `com.atproto.lexicon.schema` record, keyed by the NSID, in that DID's repo. Resolution is not hierarchical: `com.example.feed.post` needs `_lexicon.feed.example.com`, and `com.example`'s record does not cover it. `schema:check-dns` prints each record you need:

```bash
php artisan schema:check-dns --did=did:plc:abc
```

The loader follows the same steps for a lexicon it cannot find locally, when `dns_resolution.enabled` is on (see [Configuration](../getting-started/configuration.md#dns-resolution)).

## Pulling other people's lexicons

```bash
php artisan schema:pull com.other.thing
php artisan schema:pull            # every lexicon in the lock file again
php artisan schema:pull --force    # replace local copies that differ
```

`schema:pull` writes what it copies to `atp-schema.lexicon_path` and records it in `lexicons.lock.json` (`atp-schema.lock_file`): the DID, record URI and CID of each lexicon. A local copy that differs from the published version is kept unless you pass `--force`. Commit both the files and the lock file.

## Publishing your own

Publishing needs an authenticated session, so it lives in [atp-client](https://github.com/socialdept/atp-client): `php artisan lexicon:publish`.
