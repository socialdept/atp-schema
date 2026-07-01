# Changelog

## v0.4.6

### Fixed
- Nested lexicon **defs** now serialize their `$type` in the canonical AT Protocol
  fragment form `nsid#defName` instead of the dotted `nsid.defName`. The dotted
  string is still used internally for namespace/class/path resolution (it must
  pass `Nsid::parse`, which rejects `#`), but `getLexicon()` — and therefore the
  `$type` emitted by `Data::toRecord()` — is now the canonical fragment. This also
  fixes closed-union resolution of external def variants (e.g. `app.bsky.embed.images#view`),
  which previously could not be matched because the type map was keyed on the
  dotted form.

### Added
- `UnionHelper::buildTypeMap()` also registers the legacy dotted `$type` as an
  alias, so records written before this change still deserialize.
- `LexiconDocument` gained an optional `lexiconType` (and `getLexiconType()`) so a
  synthetic def document can carry its canonical fragment `$type` independently of
  its dotted id.

### Note
- Consumers that generate their own DTOs must regenerate them (`schema:generate`,
  after `schema:clear-cache --all`) to pick up the canonical def `$type`. The
  package's own bundled `src/Generated` DTOs are regenerated separately.
