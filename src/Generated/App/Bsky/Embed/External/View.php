<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Embed\External;

use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.embed.external.view
 * Type: object
 *
 * @property mixed $external
 *
 * Constraints:
 * - Required: external
 */
#[Generated(regenerate: true)]
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
            external: ViewExternal::fromArray($data['external'])
        );
    }

}
