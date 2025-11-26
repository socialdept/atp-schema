<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Feed\Defs;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Metadata about the requesting account's relationship with the subject
 * content. Only has meaningful content for authed requests.
 *
 * Lexicon: app.bsky.feed.defs.viewerState
 * Type: object
 *
 * @property string|null $repost
 * @property string|null $like
 * @property bool|null $bookmarked
 * @property bool|null $threadMuted
 * @property bool|null $replyDisabled
 * @property bool|null $embeddingDisabled
 * @property bool|null $pinned
 *
 * Constraints:
 * - repost: Format: at-uri
 * - like: Format: at-uri
 */
class ViewerState extends Data
{
    public function __construct(
        public readonly ?string $repost = null,
        public readonly ?string $like = null,
        public readonly ?bool $bookmarked = null,
        public readonly ?bool $threadMuted = null,
        public readonly ?bool $replyDisabled = null,
        public readonly ?bool $embeddingDisabled = null,
        public readonly ?bool $pinned = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.feed.defs.viewerState';
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
            repost: $data['repost'] ?? null,
            like: $data['like'] ?? null,
            bookmarked: $data['bookmarked'] ?? null,
            threadMuted: $data['threadMuted'] ?? null,
            replyDisabled: $data['replyDisabled'] ?? null,
            embeddingDisabled: $data['embeddingDisabled'] ?? null,
            pinned: $data['pinned'] ?? null
        );
    }

}
