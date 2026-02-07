<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Actor\Defs;

use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.actor.defs.adultContentPref
 * Type: object
 *
 * @property bool $enabled
 *
 * Constraints:
 * - Required: enabled
 */
#[Generated(regenerate: true)]
class AdultContentPref extends Data
{
    public function __construct(
        public readonly bool $enabled
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.actor.defs.adultContentPref';
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
            enabled: $data['enabled']
        );
    }

}
