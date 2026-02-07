<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Notification\Defs;

use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;

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
#[Generated(regenerate: true)]
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
            chat: ChatPreference::fromArray($data['chat']),
            follow: FilterablePreference::fromArray($data['follow']),
            like: FilterablePreference::fromArray($data['like']),
            likeViaRepost: FilterablePreference::fromArray($data['likeViaRepost']),
            mention: FilterablePreference::fromArray($data['mention']),
            quote: FilterablePreference::fromArray($data['quote']),
            reply: FilterablePreference::fromArray($data['reply']),
            repost: FilterablePreference::fromArray($data['repost']),
            repostViaRepost: FilterablePreference::fromArray($data['repostViaRepost']),
            starterpackJoined: Preference::fromArray($data['starterpackJoined']),
            subscribedPost: Preference::fromArray($data['subscribedPost']),
            unverified: Preference::fromArray($data['unverified']),
            verified: Preference::fromArray($data['verified'])
        );
    }

}
