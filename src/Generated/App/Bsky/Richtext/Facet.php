<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Richtext;

use SocialDept\AtpSchema\Attributes\Generated;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.richtext.facet
 * Type: object
 *
 * @property ByteSlice $index
 * @property array $features
 *
 * Constraints:
 * - Required: index, features
 */
#[Generated(regenerate: true)]
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
            index: ByteSlice::fromArray($data['index']),
            features: $data['features']
        );
    }

}
