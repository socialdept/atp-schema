<?php

namespace SocialDept\Schema\Generated\Tools\Ozone\Moderation\Defs;

use SocialDept\Schema\Data\Data;

/**
 * Lexicon: tools.ozone.moderation.defs.imageDetails
 * Type: object
 *
 * @property int $width
 * @property int $height
 *
 * Constraints:
 * - Required: width, height
 */
class ImageDetails extends Data
{
    /**
     */
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
        return 'tools.ozone.moderation.defs.imageDetails';
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
