<?php

namespace SocialDept\Schema\Generated\App\Bsky\Feed\Defs;

use SocialDept\Schema\Data\Data;

/**
 * Lexicon: app.bsky.feed.defs.skeletonReasonRepost
 * Type: object
 *
 * @property string $repost
 *
 * Constraints:
 * - Required: repost
 * - repost: Format: at-uri
 */
class SkeletonReasonRepost extends Data
{

    public function __construct(
        public readonly string $repost
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.feed.defs.skeletonReasonRepost';
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
            repost: $data['repost']
        );
    }

}
