<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Embed;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.embed.external
 * Type: object
 *
 * @property External $external
 *
 * Constraints:
 * - Required: external
 */
class External extends Data
{
    public function __construct(
        public readonly External $external
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.embed.external';
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
            external: $data['external']
        );
    }

}
