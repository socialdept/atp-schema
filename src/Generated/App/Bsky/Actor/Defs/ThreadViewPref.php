<?php

namespace SocialDept\Schema\Generated\App\Bsky\Actor\Defs;

use SocialDept\Schema\Data\Data;

/**
 * Lexicon: app.bsky.actor.defs.threadViewPref
 * Type: object
 *
 * @property string|null $sort Sorting mode for threads.
 */
class ThreadViewPref extends Data
{
    /**
     * @param  string|null  $sort  Sorting mode for threads.
     */
    public function __construct(
        public readonly ?string $sort = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.actor.defs.threadViewPref';
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
            sort: $data['sort'] ?? null
        );
    }

}
