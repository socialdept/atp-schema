<?php

namespace SocialDept\Schema\Generated\App\Bsky\Actor\Defs;

use SocialDept\Schema\Data\Data;

/**
 * The subject's followers whom you also follow
 *
 * Lexicon: app.bsky.actor.defs.knownFollowers
 * Type: object
 *
 * @property int $count
 * @property array $followers
 *
 * Constraints:
 * - Required: count, followers
 * - followers: Max length: 5
 * - followers: Min length: 0
 */
class KnownFollowers extends Data
{

    public function __construct(
        public readonly int $count,
        public readonly array $followers
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.actor.defs.knownFollowers';
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
            count: $data['count'],
            followers: $data['followers'] ?? []
        );
    }

}
