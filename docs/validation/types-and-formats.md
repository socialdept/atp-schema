# Types and formats

Part of the [atp-schema docs](../introduction.md#documentation). The lexicon types and string formats that are checked, and the matching Laravel validation rules.

## Types

The parser reads every type in the Lexicon spec into a class under `SocialDept\AtpSchema\Data\Types`:

- **Primitives:** `null`, `boolean`, `integer`, `string`, `bytes`, `cid-link` and `unknown`.
- **Containers:** `object`, `params` and `array`, with their properties, required fields and item types.
- **Unions:** `union`, open or closed, discriminated by `$type` (see [Unions](unions.md)).
- **Refs:** `ref`, to a local `#definition` or to another lexicon.
- **Blobs:** `blob`, with `accept` MIME types and `maxSize` (see [Blobs](blobs.md)).

String limits (`maxLength`, `minLength`, `maxGraphemes`, `minGraphemes`, `enum`, `const`), integer limits (`minimum`, `maximum`, `enum`, `const`) and array limits (`minLength`, `maxLength`) are checked when a record is validated.

## String formats

These `format` values are checked when you validate with the facade (see [Validating records](validating.md)):

| Format | Accepts |
|---|---|
| `datetime` | An ISO 8601 datetime |
| `uri` | Any URI with a scheme, `at://` included |
| `at-uri` | An AT URI |
| `did` | A DID |
| `handle` | A handle |
| `at-identifier` | A DID or a handle |
| `nsid` | An NSID |
| `cid` | A CID |
| `language` | A BCP 47 language tag |

Any other format, such as `tid` or `record-key`, is accepted without a check.

## Laravel validation rules

The same checks are registered as Laravel validation rules:

```php
$request->validate([
    'did' => ['required', 'did'],
    'handle' => ['required', 'handle'],
    'collection' => ['required', 'nsid'],
    'subject' => ['required', 'at_uri'],
    'cid' => ['required', 'cid'],
    'createdAt' => ['required', 'at_datetime'],
    'lang' => ['language'],
    'text' => ['required', 'max_graphemes:300', 'min_graphemes:1'],
]);
```

Each is also a rule class in `SocialDept\AtpSchema\Validation\Rules`, such as `new Did()` or `new MaxGraphemes(300)`: `AtDatetime`, `AtUri`, `Cid`, `Did`, `Handle`, `Language`, `MaxGraphemes`, `MinGraphemes` and `Nsid`.
