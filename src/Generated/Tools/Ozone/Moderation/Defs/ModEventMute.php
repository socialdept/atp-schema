<?php

namespace SocialDept\Schema\Generated\Tools\Ozone\Moderation\Defs;

use SocialDept\Schema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Mute incoming reports on a subject
 *
 * Lexicon: tools.ozone.moderation.defs.modEventMute
 * Type: object
 *
 * @property string|null $comment
 * @property int $durationInHours Indicates how long the subject should remain muted.
 *
 * Constraints:
 * - Required: durationInHours
 */
class ModEventMute extends Data
{
    /**
     * @param  int  $durationInHours  Indicates how long the subject should remain muted.
     */
    public function __construct(
        public readonly int $durationInHours,
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
        return 'tools.ozone.moderation.defs.modEventMute';
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
            durationInHours: $data['durationInHours'],
            comment: $data['comment'] ?? null
        );
    }

}
