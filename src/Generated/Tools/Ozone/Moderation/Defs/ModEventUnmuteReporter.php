<?php

namespace SocialDept\AtpSchema\Generated\Tools\Ozone\Moderation\Defs;

use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Unmute incoming reports from an account
 *
 * Lexicon: tools.ozone.moderation.defs.modEventUnmuteReporter
 * Type: object
 *
 * @property string|null $comment Describe reasoning behind the reversal.
 */
#[Generated(regenerate: true)]
class ModEventUnmuteReporter extends Data
{
    /**
     * @param  string|null  $comment  Describe reasoning behind the reversal.
     */
    public function __construct(
        public readonly ?string $comment = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.modEventUnmuteReporter';
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
            comment: $data['comment'] ?? null
        );
    }

}
