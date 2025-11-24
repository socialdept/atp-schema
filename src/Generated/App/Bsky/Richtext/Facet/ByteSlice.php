<?php

namespace SocialDept\Schema\Generated\App\Bsky\Richtext\Facet;

use SocialDept\Schema\Data\Data;

/**
 * Specifies the sub-string range a facet feature applies to. Start index is
 * inclusive, end index is exclusive. Indices are zero-indexed, counting bytes
 * of the UTF-8 encoded text. NOTE: some languages, like Javascript, use UTF-16
 * or Unicode codepoints for string slice indexing; in these languages, convert
 * to byte arrays before working with facets.
 *
 * Lexicon: app.bsky.richtext.facet.byteSlice
 * Type: object
 *
 * @property int $byteStart
 * @property int $byteEnd
 *
 * Constraints:
 * - Required: byteStart, byteEnd
 * - byteStart: Minimum: 0
 * - byteEnd: Minimum: 0
 */
class ByteSlice extends Data
{

    public function __construct(
        public readonly int $byteStart,
        public readonly int $byteEnd
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.richtext.facet.byteSlice';
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
            byteStart: $data['byteStart'],
            byteEnd: $data['byteEnd']
        );
    }

}
