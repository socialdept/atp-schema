<?php

namespace SocialDept\Schema\Generated\App\Bsky\Feed\Defs;

use SocialDept\Schema\Data\Data;

/**
 * Lexicon: app.bsky.feed.defs.interaction
 * Type: object
 *
 * @property string|null $item
 * @property string|null $event
 * @property string|null $feedContext Context on a feed item that was originally supplied by the feed generator on getFeedSkeleton.
 * @property string|null $reqId Unique identifier per request that may be passed back alongside interactions.
 *
 * Constraints:
 * - item: Format: at-uri
 * - feedContext: Max length: 2000
 * - reqId: Max length: 100
 */
class Interaction extends Data
{

    /**
     * @param  string|null  $feedContext  Context on a feed item that was originally supplied by the feed generator on getFeedSkeleton.
     * @param  string|null  $reqId  Unique identifier per request that may be passed back alongside interactions.
     */
    public function __construct(
        public readonly ?string $item = null,
        public readonly ?string $event = null,
        public readonly ?string $feedContext = null,
        public readonly ?string $reqId = null
    ) {}

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.feed.defs.interaction';
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
            item: $data['item'] ?? null,
            event: $data['event'] ?? null,
            feedContext: $data['feedContext'] ?? null,
            reqId: $data['reqId'] ?? null
        );
    }

}
