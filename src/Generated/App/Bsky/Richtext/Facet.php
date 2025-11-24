<?php

namespace SocialDept\Schema\Generated\App\Bsky\Richtext;

use SocialDept\Schema\Data\Data;

/**
 * Lexicon: app.bsky.richtext.facet
 * Type: object
 *
 * @property ByteSlice $index
 * @property array $features
 *
 * Constraints:
 * - Required: index, features
 */
class Facet extends Data
{

    public function __construct(
        public readonly ByteSlice $index,
        public readonly array $features
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.richtext.facet';
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
            index: $data['index'],
            features: $data['features']
        );
    }

}
