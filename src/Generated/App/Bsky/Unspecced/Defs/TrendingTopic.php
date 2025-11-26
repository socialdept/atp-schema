<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Unspecced\Defs;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.unspecced.defs.trendingTopic
 * Type: object
 *
 * @property string $topic
 * @property string|null $displayName
 * @property string|null $description
 * @property string $link
 *
 * Constraints:
 * - Required: topic, link
 */
class TrendingTopic extends Data
{
    public function __construct(
        public readonly string $topic,
        public readonly string $link,
        public readonly ?string $displayName = null,
        public readonly ?string $description = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.unspecced.defs.trendingTopic';
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
            topic: $data['topic'],
            link: $data['link'],
            displayName: $data['displayName'] ?? null,
            description: $data['description'] ?? null
        );
    }

}
