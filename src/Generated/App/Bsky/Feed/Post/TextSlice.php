<?php

namespace SocialDept\Schema\Generated\App\Bsky\Feed\Post;

use SocialDept\Schema\Data\Data;

/**
 * Deprecated. Use app.bsky.richtext instead -- A text segment. Start is
 * inclusive, end is exclusive. Indices are for utf16-encoded strings.
 *
 * Lexicon: app.bsky.feed.post.textSlice
 * Type: object
 *
 * @property int $start
 * @property int $end
 *
 * Constraints:
 * - Required: start, end
 * - start: Minimum: 0
 * - end: Minimum: 0
 */
class TextSlice extends Data
{

    /**
     */
    public function __construct(
        public readonly int $start,
        public readonly int $end
    ) {}

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.feed.post.textSlice';
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
            start: $data['start'],
            end: $data['end']
        );
    }

}
