<?php

namespace SocialDept\Schema\Generated\App\Bsky\Feed\Defs;

use SocialDept\Schema\Data\Data;

/**
 * Metadata about this post within the context of the thread it is in.
 *
 * Lexicon: app.bsky.feed.defs.threadContext
 * Type: object
 *
 * @property string|null $rootAuthorLike
 *
 * Constraints:
 * - rootAuthorLike: Format: at-uri
 */
class ThreadContext extends Data
{

    public function __construct(
        public readonly ?string $rootAuthorLike = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.feed.defs.threadContext';
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
            rootAuthorLike: $data['rootAuthorLike'] ?? null
        );
    }

}
