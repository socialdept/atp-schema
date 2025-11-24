<?php

namespace SocialDept\Schema\Generated\App\Bsky\Embed\External;

use SocialDept\Schema\Data\Data;

/**
 * Lexicon: app.bsky.embed.external.view
 * Type: object
 *
 * @property mixed $external
 *
 * Constraints:
 * - Required: external
 */
class View extends Data
{

    public function __construct(
        public readonly mixed $external
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.embed.external.view';
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
