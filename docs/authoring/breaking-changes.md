# Breaking changes

Part of the [atp-schema docs](../introduction.md#documentation). What a published lexicon may and may not change, and checking it from code.

A published lexicon may grow but not change shape. New fields must be optional, types cannot change, required and nullable fields cannot change, enums, consts and string formats are fixed, a record's key type is fixed, a body's encoding is fixed, and a union cannot switch between open and closed or, when closed, gain or lose types. Changed length limits, integer ranges, blob types and sizes, and refs are warnings. Adding or removing an optional field or a whole definition is fine.

| Finding | Level |
|---|---|
| `type-change`, `record-key-type`, `body-encoding` | Error |
| `object-required`, `params-required`, `object-nullable` | Error |
| `enum-values`, `const-value`, `string-format` | Error |
| `union-open-closed`, `union-closed-refs` | Error |
| `string-length`, `bytes-length`, `array-length`, `integer-range` | Warning |
| `blob-accept`, `blob-size`, `ref-change` | Warning |

`schema:breaking` fails on an error and lists warnings (see [Authoring commands](commands.md)). The same check runs from code:

```php
use SocialDept\AtpSchema\Authoring\BreakingChanges;

$issues = BreakingChanges::between($published, $edited);   // both decoded lexicons, returns an array of Issue
```
