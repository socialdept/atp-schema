<?php

namespace SocialDept\Schema\Generated\App\Bsky\Graph\Defs;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\App\Bsky\Actor\ProfileViewBasic;
use SocialDept\Schema\Generated\App\Bsky\Feed\GeneratorView;
use SocialDept\Schema\Generated\Com\Atproto\Label\Label;

/**
 * Lexicon: app.bsky.graph.defs.starterPackView
 * Type: object
 *
 * @property string $uri
 * @property string $cid
 * @property mixed $record
 * @property ProfileViewBasic $creator
 * @property mixed $list
 * @property array|null $listItemsSample
 * @property array<GeneratorView>|null $feeds
 * @property int|null $joinedWeekCount
 * @property int|null $joinedAllTimeCount
 * @property array<Label>|null $labels
 * @property Carbon $indexedAt
 *
 * Constraints:
 * - Required: uri, cid, record, creator, indexedAt
 * - uri: Format: at-uri
 * - cid: Format: cid
 * - listItemsSample: Max length: 12
 * - feeds: Max length: 3
 * - joinedWeekCount: Minimum: 0
 * - joinedAllTimeCount: Minimum: 0
 * - indexedAt: Format: datetime
 */
class StarterPackView extends Data
{

    public function __construct(
        public readonly string $uri,
        public readonly string $cid,
        public readonly mixed $record,
        public readonly ProfileViewBasic $creator,
        public readonly Carbon $indexedAt,
        public readonly mixed $list = null,
        public readonly ?array $listItemsSample = null,
        public readonly ?array $feeds = null,
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
        return 'app.bsky.graph.defs.starterPackView';
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
            list: $data['list'] ?? null,
            listItemsSample: $data['listItemsSample'] ?? [],
            feeds: isset($data['feeds']) ? array_map(fn ($item) => Defs::fromArray($item), $data['feeds']) : [],
            joinedWeekCount: $data['joinedWeekCount'] ?? null,
            joinedAllTimeCount: $data['joinedAllTimeCount'] ?? null,
            labels: isset($data['labels']) ? array_map(fn ($item) => Defs::fromArray($item), $data['labels']) : []
        );
    }

}
