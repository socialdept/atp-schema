<?php

namespace SocialDept\Schema\Generated\App\Bsky\Unspecced\Defs;

use SocialDept\Schema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.unspecced.defs.threadItemNotFound
 * Type: object
 */
class ThreadItemNotFound extends Data
{
    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.unspecced.defs.threadItemNotFound';
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
