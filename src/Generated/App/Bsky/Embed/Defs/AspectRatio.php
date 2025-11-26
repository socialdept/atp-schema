<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Embed\Defs;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * width:height represents an aspect ratio. It may be approximate, and may not
 * correspond to absolute dimensions in any given unit.
 *
 * Lexicon: app.bsky.embed.defs.aspectRatio
 * Type: object
 *
 * @property int $width
 * @property int $height
 *
 * Constraints:
 * - Required: width, height
 * - width: Minimum: 1
 * - height: Minimum: 1
 */
class AspectRatio extends Data
{
    public function __construct(
        public readonly int $width,
        public readonly int $height
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.embed.defs.aspectRatio';
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
            width: $data['width'],
            height: $data['height']
        );
    }

}
