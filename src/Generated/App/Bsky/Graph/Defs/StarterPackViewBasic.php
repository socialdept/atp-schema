<?php

namespace SocialDept\Schema\Generated\App\Bsky\Graph\Defs;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\App\Bsky\Actor\ProfileViewBasic;
use SocialDept\Schema\Generated\Com\Atproto\Label\Label;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.graph.defs.starterPackViewBasic
 * Type: object
 *
 * @property string $uri
 * @property string $cid
 * @property mixed $record
 * @property ProfileViewBasic $creator
 * @property int|null $listItemCount
 * @property int|null $joinedWeekCount
 * @property int|null $joinedAllTimeCount
 * @property array<Label>|null $labels
 * @property Carbon $indexedAt
 *
 * Constraints:
 * - Required: uri, cid, record, creator, indexedAt
 * - uri: Format: at-uri
 * - cid: Format: cid
 * - listItemCount: Minimum: 0
 * - joinedWeekCount: Minimum: 0
 * - joinedAllTimeCount: Minimum: 0
 * - indexedAt: Format: datetime
 */
class StarterPackViewBasic extends Data
{
    public function __construct(
        public readonly string $uri,
        public readonly string $cid,
        public readonly mixed $record,
        public readonly ProfileViewBasic $creator,
        public readonly Carbon $indexedAt,
        public readonly ?int $listItemCount = null,
        public readonly ?int $joinedWeekCount = null,
        public readonly ?int $joinedAllTimeCount = null,
        public readonly ?array $labels = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.graph.defs.starterPackViewBasic';
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
            record: $data['record'],
            creator: Defs::fromArray($data['creator']),
            indexedAt: Carbon::parse($data['indexedAt']),
            listItemCount: $data['listItemCount'] ?? null,
            joinedWeekCount: $data['joinedWeekCount'] ?? null,
            joinedAllTimeCount: $data['joinedAllTimeCount'] ?? null,
            labels: isset($data['labels']) ? array_map(fn ($item) => Defs::fromArray($item), $data['labels']) : []
        );
    }

}
