<?php

namespace SocialDept\Schema\Generated\App\Bsky\Unspecced\Defs;

use SocialDept\Schema\Data\Data;

/**
 * Lexicon: app.bsky.unspecced.defs.skeletonSearchStarterPack
 * Type: object
 *
 * @property string $uri
 *
 * Constraints:
 * - Required: uri
 * - uri: Format: at-uri
 */
class SkeletonSearchStarterPack extends Data
{
    /**
     */
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
        return 'app.bsky.unspecced.defs.skeletonSearchStarterPack';
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
