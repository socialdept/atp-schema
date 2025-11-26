<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Graph\Defs;

use Carbon\Carbon;
use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Generated\Com\Atproto\Label\Defs\Label;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.graph.defs.listViewBasic
 * Type: object
 *
 * @property string $uri
 * @property string $cid
 * @property string $name
 * @property mixed $purpose
 * @property string|null $avatar
 * @property int|null $listItemCount
 * @property array<Label>|null $labels
 * @property mixed $viewer
 * @property Carbon|null $indexedAt
 *
 * Constraints:
 * - Required: uri, cid, name, purpose
 * - uri: Format: at-uri
 * - cid: Format: cid
 * - name: Max length: 64
 * - name: Min length: 1
 * - avatar: Format: uri
 * - listItemCount: Minimum: 0
 * - indexedAt: Format: datetime
 */
class ListViewBasic extends Data
{
    public function __construct(
        public readonly string $uri,
        public readonly string $cid,
        public readonly string $name,
        public readonly mixed $purpose,
        public readonly ?string $avatar = null,
        public readonly ?int $listItemCount = null,
        public readonly ?array $labels = null,
        public readonly mixed $viewer = null,
        public readonly ?Carbon $indexedAt = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.graph.defs.listViewBasic';
    }


    /**
     * Create an instance from an array.
     *
     * @param  array  $data  The data array
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new static(
            uri: $data['uri'],
            cid: $data['cid'],
            name: $data['name'],
            purpose: $data['purpose'],
            avatar: $data['avatar'] ?? null,
            listItemCount: $data['listItemCount'] ?? null,
            labels: isset($data['labels']) ? array_map(fn ($item) => Label::fromArray($item), $data['labels']) : [],
            viewer: $data['viewer'] ?? null,
            indexedAt: isset($data['indexedAt']) ? Carbon::parse($data['indexedAt']) : null
        );
    }

}
