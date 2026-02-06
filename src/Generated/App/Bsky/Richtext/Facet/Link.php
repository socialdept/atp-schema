<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Richtext\Facet;

use SocialDept\AtpSchema\Attributes\Generated;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Facet feature for a URL. The text URL may have been simplified or truncated,
 * but the facet reference should be a complete URL.
 *
 * Lexicon: app.bsky.richtext.facet.link
 * Type: object
 *
 * @property string $uri
 *
 * Constraints:
 * - Required: uri
 * - uri: Format: uri
 */
#[Generated(regenerate: true)]
class Link extends Data
{
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
        return 'app.bsky.richtext.facet.link';
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
