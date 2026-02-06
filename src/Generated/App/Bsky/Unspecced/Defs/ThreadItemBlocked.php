<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Unspecced\Defs;

use SocialDept\AtpSchema\Attributes\Generated;

use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Generated\App\Bsky\Feed\Defs\BlockedAuthor;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.unspecced.defs.threadItemBlocked
 * Type: object
 *
 * @property BlockedAuthor $author
 *
 * Constraints:
 * - Required: author
 */
#[Generated(regenerate: true)]
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
            author: BlockedAuthor::fromArray($data['author'])
        );
    }

}
