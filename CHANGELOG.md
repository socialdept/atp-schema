# Changelog

## v0.4.7

### Fixed
- Generated parent DTOs now emit a correct `use` import for the nested sub-object
  classes they reference. Previously an **object-main** lexicon whose main
  definition referenced its own sub-object def (e.g. a `#colors` object) emitted
  the import in the sibling namespace (`…\Colors`) instead of nested under the
  parent class (`…\Theme\Colors`). Because that wrong import shared the parent's
  namespace it was silently dropped by the same-namespace filter, leaving
  `Colors::fromArray()` to resolve against the parent's namespace where the class
  does not exist — a fatal `Class "…" not found` the moment the parent's
  `fromArray()` ran. Nesting is now keyed off whether the ref is one of the
  document's own sub-definitions (record and object mains alike), rather than the
  previous record-only heuristic. Cross-references between sibling sub-defs
  continue to resolve in their shared namespace.

### Note
- Consumers with hand-patched generated DTOs (manually added `use` imports for
  nested sub-objects) can regenerate with `schema:generate` after upgrading and
  drop the hand-fix.

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
