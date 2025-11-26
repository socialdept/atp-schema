<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Richtext\Facet;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Facet feature for mention of another account. The text is usually a handle,
 * including a '@' prefix, but the facet reference is a DID.
 *
 * Lexicon: app.bsky.richtext.facet.mention
 * Type: object
 *
 * @property string $did
 *
 * Constraints:
 * - Required: did
 * - did: Format: did
 */
class Mention extends Data
{
    public function __construct(
        public readonly string $did
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.richtext.facet.mention';
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
            did: $data['did']
        );
    }

}
