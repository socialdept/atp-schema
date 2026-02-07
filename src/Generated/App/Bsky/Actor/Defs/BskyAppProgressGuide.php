<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Actor\Defs;

use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * If set, an active progress guide. Once completed, can be set to undefined.
 * Should have unspecced fields tracking progress.
 *
 * Lexicon: app.bsky.actor.defs.bskyAppProgressGuide
 * Type: object
 *
 * @property string $guide
 *
 * Constraints:
 * - Required: guide
 * - guide: Max length: 100
 */
#[Generated(regenerate: true)]
class BskyAppProgressGuide extends Data
{
    public function __construct(
        public readonly string $guide
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.actor.defs.bskyAppProgressGuide';
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
            guide: $data['guide']
        );
    }

}
