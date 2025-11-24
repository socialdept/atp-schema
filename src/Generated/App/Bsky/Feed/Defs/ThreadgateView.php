<?php

namespace SocialDept\Schema\Generated\App\Bsky\Feed\Defs;

use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\App\Bsky\Graph\ListViewBasic;

/**
 * Lexicon: app.bsky.feed.defs.threadgateView
 * Type: object
 *
 * @property string|null $uri
 * @property string|null $cid
 * @property mixed $record
 * @property array<ListViewBasic>|null $lists
 *
 * Constraints:
 * - uri: Format: at-uri
 * - cid: Format: cid
 */
class ThreadgateView extends Data
{

    public function __construct(
        public readonly ?string $uri = null,
        public readonly ?string $cid = null,
        public readonly mixed $record = null,
        public readonly ?array $lists = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.feed.defs.threadgateView';
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
            uri: $data['uri'] ?? null,
            cid: $data['cid'] ?? null,
            record: $data['record'] ?? null,
            lists: isset($data['lists']) ? array_map(fn ($item) => Defs::fromArray($item), $data['lists']) : []
        );
    }

}
