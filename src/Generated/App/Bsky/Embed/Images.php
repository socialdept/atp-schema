<?php

namespace SocialDept\Schema\Generated\App\Bsky\Embed;

use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\App\Bsky\Embed\Images\Image;

/**
 * A set of images embedded in a Bluesky record (eg, a post).
 *
 * Lexicon: app.bsky.embed.images
 * Type: object
 *
 * @property array<Image> $images
 *
 * Constraints:
 * - Required: images
 * - images: Max length: 4
 */
class Images extends Data
{
    /**
     */
    public function __construct(
        public readonly array $images
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.embed.images';
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
            images: $data['images'] ?? []
        );
    }

}
