<?php

namespace SocialDept\Schema\Generated\App\Bsky\Unspecced\Defs;

use SocialDept\Schema\Data\Data;

/**
 * Lexicon: app.bsky.unspecced.defs.threadItemNoUnauthenticated
 * Type: object
 */
class ThreadItemNoUnauthenticated extends Data
{


    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.unspecced.defs.threadItemNoUnauthenticated';
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
