# Authoring commands

Part of the [atp-schema docs](../introduction.md#documentation). Creating, linting and diffing your own lexicons from the command line.

The commands work on the files in `atp-schema.lexicon_path`, `resources/lexicons` by default, where `com.example.thing` lives at `com/example/thing.json`. The rules and finding names follow Bluesky's own tooling (`goat lex`), so both report the same things.

```bash
php artisan schema:new record com.example.thing   # record, query, procedure or permission-set
php artisan schema:lint                           # every local file, or name some
php artisan schema:diff com.example.thing         # against the published version
php artisan schema:breaking                       # changes that would break the published versions
php artisan schema:pull com.other.thing           # copy someone else's lexicon in, and lock it
php artisan schema:check-dns --did=did:plc:abc    # the _lexicon TXT records you are missing
```

| Command | Fails when |
|---|---|
| `schema:new` | The NSID is not valid, or the file exists and `--force` is not passed |
| `schema:lint` | A file has an error. Warnings and notes are listed but pass |
| `schema:breaking` | A change would break the published version |
| `schema:pull` | A lexicon is not published, or its authority cannot be read |
| `schema:check-dns` | A TXT record is missing, or names another DID than `--did` |

`lint` and `breaking` take `--json` for one finding per line. `diff` and `breaking` take `--against=<file>` to compare with a file instead of the network. `schema:new` writes a template with TODOs to fill in, then you run `schema:lint` on it.

## From code

The linter returns the same findings as `schema:lint`, as `Issue` objects:

```php
use SocialDept\AtpSchema\Authoring\Issue;
use SocialDept\AtpSchema\Authoring\LexiconLinter;

$issues = (new LexiconLinter())->lint($lexicon);   // the lexicon as a decoded array

Issue::anyErrors($issues);                          // true when one is an error

foreach ($issues as $issue) {
    $issue->level;     // Issue::ERROR, Issue::WARN or Issue::INFO
    $issue->name;      // the finding's name, as goat lex reports it
    $issue->message;
}
```
