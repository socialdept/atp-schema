<?php

namespace SocialDept\AtpSchema\Authoring;

/**
 * Which network lexicons were pulled, from where, and at which version, so a
 * pull is repeatable and a changed upstream schema is noticed.
 *
 *     {"lexicons": {"com.example.thing": {"did": "...", "uri": "...", "cid": "..."}}}
 */
class LexiconLock
{
    /**
     * @var array<string, array{did: string, uri: string, cid: string|null}>
     */
    protected array $entries;

    public function __construct(
        protected string $path,
    ) {
        $data = is_file($path) ? json_decode((string) file_get_contents($path), true) : null;
        $this->entries = is_array($data['lexicons'] ?? null) ? $data['lexicons'] : [];
    }

    public static function fromConfig(): self
    {
        return new self((string) (config('atp-schema.lock_file') ?? base_path('lexicons.lock.json')));
    }

    /**
     * @return array{did: string, uri: string, cid: string|null}|null
     */
    public function get(string $nsid): ?array
    {
        return $this->entries[$nsid] ?? null;
    }

    /**
     * @return array<string, array{did: string, uri: string, cid: string|null}>
     */
    public function all(): array
    {
        return $this->entries;
    }

    public function put(PublishedLexicon $lexicon): void
    {
        $this->entries[$lexicon->nsid] = ['did' => $lexicon->did, 'uri' => $lexicon->uri, 'cid' => $lexicon->cid];
    }

    public function save(): void
    {
        ksort($this->entries);

        file_put_contents($this->path, json_encode(['lexicons' => $this->entries], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n");
    }
}
