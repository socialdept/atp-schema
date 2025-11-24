<?php

namespace SocialDept\Schema\Generated\App\Bsky\Richtext\Facet;

use SocialDept\Schema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Facet feature for a hashtag. The text usually includes a '#' prefix, but the
 * facet reference should not (except in the case of 'double hash tags').
 *
 * Lexicon: app.bsky.richtext.facet.tag
 * Type: object
 *
 * @property string $tag
 *
 * Constraints:
 * - Required: tag
 * - tag: Max length: 640
 * - tag: Max graphemes: 64
 */
class Tag extends Data
{
    public function __construct(
        public readonly string $tag
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.richtext.facet.tag';
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
            tag: $data['tag']
        );
    }

}
