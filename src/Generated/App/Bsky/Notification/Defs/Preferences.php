<?php

namespace SocialDept\Schema\Generated\App\Bsky\Notification\Defs;

use SocialDept\Schema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.notification.defs.preferences
 * Type: object
 *
 * @property mixed $chat
 * @property mixed $follow
 * @property mixed $like
 * @property mixed $likeViaRepost
 * @property mixed $mention
 * @property mixed $quote
 * @property mixed $reply
 * @property mixed $repost
 * @property mixed $repostViaRepost
 * @property mixed $starterpackJoined
 * @property mixed $subscribedPost
 * @property mixed $unverified
 * @property mixed $verified
 *
 * Constraints:
 * - Required: chat, follow, like, likeViaRepost, mention, quote, reply, repost, repostViaRepost, starterpackJoined, subscribedPost, unverified, verified
 */
class Preferences extends Data
{
    public function __construct(
        public readonly mixed $chat,
        public readonly mixed $follow,
        public readonly mixed $like,
        public readonly mixed $likeViaRepost,
        public readonly mixed $mention,
        public readonly mixed $quote,
        public readonly mixed $reply,
        public readonly mixed $repost,
        public readonly mixed $repostViaRepost,
        public readonly mixed $starterpackJoined,
        public readonly mixed $subscribedPost,
        public readonly mixed $unverified,
        public readonly mixed $verified
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.notification.defs.preferences';
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
            chat: $data['chat'],
            follow: $data['follow'],
            like: $data['like'],
            likeViaRepost: $data['likeViaRepost'],
            mention: $data['mention'],
            quote: $data['quote'],
            reply: $data['reply'],
            repost: $data['repost'],
            repostViaRepost: $data['repostViaRepost'],
            starterpackJoined: $data['starterpackJoined'],
            subscribedPost: $data['subscribedPost'],
            unverified: $data['unverified'],
            verified: $data['verified']
        );
    }

}
