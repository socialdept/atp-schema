<?php

namespace SocialDept\Schema\Generated\App\Bsky\Actor\Defs;

use SocialDept\Schema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.actor.defs.feedViewPref
 * Type: object
 *
 * @property string $feed The URI of the feed, or an identifier which describes the feed.
 * @property bool|null $hideReplies Hide replies in the feed.
 * @property bool|null $hideRepliesByUnfollowed Hide replies in the feed if they are not by followed users.
 * @property int|null $hideRepliesByLikeCount Hide replies in the feed if they do not have this number of likes.
 * @property bool|null $hideReposts Hide reposts in the feed.
 * @property bool|null $hideQuotePosts Hide quote posts in the feed.
 *
 * Constraints:
 * - Required: feed
 */
class FeedViewPref extends Data
{
    /**
     * @param  string  $feed  The URI of the feed, or an identifier which describes the feed.
     * @param  bool|null  $hideReplies  Hide replies in the feed.
     * @param  bool|null  $hideRepliesByUnfollowed  Hide replies in the feed if they are not by followed users.
     * @param  int|null  $hideRepliesByLikeCount  Hide replies in the feed if they do not have this number of likes.
     * @param  bool|null  $hideReposts  Hide reposts in the feed.
     * @param  bool|null  $hideQuotePosts  Hide quote posts in the feed.
     */
    public function __construct(
        public readonly string $feed,
        public readonly ?bool $hideReplies = null,
        public readonly ?bool $hideRepliesByUnfollowed = null,
        public readonly ?int $hideRepliesByLikeCount = null,
        public readonly ?bool $hideReposts = null,
        public readonly ?bool $hideQuotePosts = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.actor.defs.feedViewPref';
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
            feed: $data['feed'],
            hideReplies: $data['hideReplies'] ?? null,
            hideRepliesByUnfollowed: $data['hideRepliesByUnfollowed'] ?? null,
            hideRepliesByLikeCount: $data['hideRepliesByLikeCount'] ?? null,
            hideReposts: $data['hideReposts'] ?? null,
            hideQuotePosts: $data['hideQuotePosts'] ?? null
        );
    }

}
