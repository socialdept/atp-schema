<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Embed\Images;

use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.embed.images.view
 * Type: object
 *
 * @property array $images
 *
 * Constraints:
 * - Required: images
 * - images: Max length: 4
 */
#[Generated(regenerate: true)]
class View extends Data
{
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
        return 'app.bsky.embed.images.view';
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
            images: isset($data['images']) ? array_map(fn ($item) => ViewImage::fromArray($item), $data['images']) : []
        );
    }

}
