<?php

namespace SocialDept\Schema\Generated\App\Bsky\Unspecced\Defs;

use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\App\Bsky\Feed\BlockedAuthor;

/**
 * Lexicon: app.bsky.unspecced.defs.threadItemBlocked
 * Type: object
 *
 * @property BlockedAuthor $author
 *
 * Constraints:
 * - Required: author
 */
class ThreadItemBlocked extends Data
{

    public function __construct(
        public readonly BlockedAuthor $author
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.unspecced.defs.threadItemBlocked';
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
            author: Defs::fromArray($data['author'])
        );
    }

}
