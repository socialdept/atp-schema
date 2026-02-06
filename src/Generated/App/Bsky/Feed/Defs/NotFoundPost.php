<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Feed\Defs;

use SocialDept\AtpSchema\Attributes\Generated;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.feed.defs.notFoundPost
 * Type: object
 *
 * @property string $uri
 * @property bool $notFound
 *
 * Constraints:
 * - Required: uri, notFound
 * - uri: Format: at-uri
 * - notFound: Const: true
 */
#[Generated(regenerate: true)]
class NotFoundPost extends Data
{
    public function __construct(
        public readonly string $uri,
        public readonly bool $notFound
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.feed.defs.notFoundPost';
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
            notFound: $data['notFound']
        );
    }

}
