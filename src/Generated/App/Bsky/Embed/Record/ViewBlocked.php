<?php

namespace SocialDept\Schema\Generated\App\Bsky\Embed\Record;

use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\App\Bsky\Feed\Defs\BlockedAuthor;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.embed.record.viewBlocked
 * Type: object
 *
 * @property string $uri
 * @property bool $blocked
 * @property BlockedAuthor $author
 *
 * Constraints:
 * - Required: uri, blocked, author
 * - uri: Format: at-uri
 * - blocked: Const: true
 */
class ViewBlocked extends Data
{
    public function __construct(
        public readonly string $uri,
        public readonly bool $blocked,
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
        return 'app.bsky.embed.record.viewBlocked';
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
            author: BlockedAuthor::fromArray($data['author'])
        );
    }

}
