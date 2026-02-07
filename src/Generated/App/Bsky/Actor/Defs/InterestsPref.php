<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Actor\Defs;

use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.actor.defs.interestsPref
 * Type: object
 *
 * @property array<string> $tags A list of tags which describe the account owner's interests gathered during onboarding.
 *
 * Constraints:
 * - Required: tags
 * - tags: Max length: 100
 */
#[Generated(regenerate: true)]
class InterestsPref extends Data
{
    /**
     * @param  array<string>  $tags  A list of tags which describe the account owner's interests gathered during onboarding.
     */
    public function __construct(
        public readonly array $tags
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.actor.defs.interestsPref';
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
            tags: $data['tags']
        );
    }

}
