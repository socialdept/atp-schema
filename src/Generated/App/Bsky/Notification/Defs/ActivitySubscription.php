<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Notification\Defs;

use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.notification.defs.activitySubscription
 * Type: object
 *
 * @property bool $post
 * @property bool $reply
 *
 * Constraints:
 * - Required: post, reply
 */
#[Generated(regenerate: true)]
class ActivitySubscription extends Data
{
    public function __construct(
        public readonly bool $post,
        public readonly bool $reply
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.notification.defs.activitySubscription';
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
            reply: $data['reply']
        );
    }

}
