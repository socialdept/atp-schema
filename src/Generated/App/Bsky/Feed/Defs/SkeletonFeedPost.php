<?php

namespace SocialDept\Schema\Generated\App\Bsky\Feed\Defs;

use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Support\UnionHelper;

/**
 * Lexicon: app.bsky.feed.defs.skeletonFeedPost
 * Type: object
 *
 * @property string $post
 * @property mixed $reason
 * @property string|null $feedContext Context that will be passed through to client and may be passed to feed generator back alongside interactions.
 *
 * Constraints:
 * - Required: post
 * - post: Format: at-uri
 * - feedContext: Max length: 2000
 */
class SkeletonFeedPost extends Data
{

    /**
     * @param  string|null  $feedContext  Context that will be passed through to client and may be passed to feed generator back alongside interactions.
     */
    public function __construct(
        public readonly string $post,
        public readonly mixed $reason = null,
        public readonly ?string $feedContext = null
    ) {}

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.feed.defs.skeletonFeedPost';
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
            post: $data['post'],
            reason: isset($data['reason']) ? UnionHelper::validateOpenUnion($data['reason']) : null,
            feedContext: $data['feedContext'] ?? null
        );
    }

}
