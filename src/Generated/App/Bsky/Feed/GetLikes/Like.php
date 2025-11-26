<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Feed\GetLikes;

use Carbon\Carbon;
use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Generated\App\Bsky\Actor\Defs\ProfileView;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.feed.getLikes.like
 * Type: object
 *
 * @property Carbon $indexedAt
 * @property Carbon $createdAt
 * @property ProfileView $actor
 *
 * Constraints:
 * - Required: indexedAt, createdAt, actor
 * - indexedAt: Format: datetime
 * - createdAt: Format: datetime
 */
class Like extends Data
{
    public function __construct(
        public readonly Carbon $indexedAt,
        public readonly Carbon $createdAt,
        public readonly ProfileView $actor
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.feed.getLikes.like';
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
            indexedAt: Carbon::parse($data['indexedAt']),
            createdAt: Carbon::parse($data['createdAt']),
            actor: ProfileView::fromArray($data['actor'])
        );
    }

}
