<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Actor\Defs;

use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.actor.defs.hiddenPostsPref
 * Type: object
 *
 * @property array<string> $items A list of URIs of posts the account owner has hidden.
 *
 * Constraints:
 * - Required: items
 */
#[Generated(regenerate: true)]
class HiddenPostsPref extends Data
{
    /**
     * @param  array<string>  $items  A list of URIs of posts the account owner has hidden.
     */
    public function __construct(
        public readonly array $items
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.actor.defs.hiddenPostsPref';
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
            items: $data['items']
        );
    }

}
