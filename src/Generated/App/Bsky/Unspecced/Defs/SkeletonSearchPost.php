<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Unspecced\Defs;

use SocialDept\AtpSchema\Attributes\Generated;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.unspecced.defs.skeletonSearchPost
 * Type: object
 *
 * @property string $uri
 *
 * Constraints:
 * - Required: uri
 * - uri: Format: at-uri
 */
#[Generated(regenerate: true)]
class SkeletonSearchPost extends Data
{
    public function __construct(
        public readonly string $uri
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.unspecced.defs.skeletonSearchPost';
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
            uri: $data['uri']
        );
    }

}
