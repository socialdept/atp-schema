<?php

namespace SocialDept\Schema\Generated\App\Bsky\Feed\Defs;

use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\App\Bsky\Feed\Defs\FeedViewPost\PostView;
use SocialDept\Schema\Generated\App\Bsky\Feed\Defs\FeedViewPost\ReplyRef;
use SocialDept\Schema\Support\UnionHelper;

/**
 * Lexicon: app.bsky.feed.defs.feedViewPost
 * Type: object
 *
 * @property mixed $post
 * @property mixed $reply
 * @property mixed $reason
 * @property string|null $feedContext Context provided by feed generator that may be passed back alongside interactions.
 * @property string|null $reqId Unique identifier per request that may be passed back alongside interactions.
 *
 * Constraints:
 * - Required: post
 * - feedContext: Max length: 2000
 * - reqId: Max length: 100
 */
class FeedViewPost extends Data
{

    /**
     * @param  string|null  $feedContext  Context provided by feed generator that may be passed back alongside interactions.
     * @param  string|null  $reqId  Unique identifier per request that may be passed back alongside interactions.
     */
    public function __construct(
        public readonly mixed $post,
        public readonly mixed $reply = null,
        public readonly mixed $reason = null,
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
        return 'app.bsky.feed.defs.feedViewPost';
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
            reply: $data['reply'] ?? null,
            reason: isset($data['reason']) ? UnionHelper::validateOpenUnion($data['reason']) : null,
            feedContext: $data['feedContext'] ?? null,
            reqId: $data['reqId'] ?? null
        );
    }

}
