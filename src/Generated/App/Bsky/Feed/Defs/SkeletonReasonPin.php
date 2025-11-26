<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Feed\Defs;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.feed.defs.skeletonReasonPin
 * Type: object
 */
class SkeletonReasonPin extends Data
{
    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.feed.defs.skeletonReasonPin';
    }


    /**
     * Create an instance from an array.
     *
     * @param  array  $data  The data array
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new static();
    }

}
