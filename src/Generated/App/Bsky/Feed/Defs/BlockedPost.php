<?php

namespace SocialDept\Schema\Generated\App\Bsky\Feed\Defs;

use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\App\Bsky\Feed\Defs\BlockedPost\BlockedAuthor;

/**
 * Lexicon: app.bsky.feed.defs.blockedPost
 * Type: object
 *
 * @property string $uri
 * @property bool $blocked
 * @property mixed $author
 *
 * Constraints:
 * - Required: uri, blocked, author
 * - uri: Format: at-uri
 * - blocked: Const: true
 */
class BlockedPost extends Data
{

    /**
     */
    public function __construct(
        public readonly string $uri,
        public readonly bool $blocked,
        public readonly mixed $author
    ) {}

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.feed.defs.blockedPost';
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
            uri: $data['uri'],
            blocked: $data['blocked'],
            author: $data['author']
        );
    }

}
