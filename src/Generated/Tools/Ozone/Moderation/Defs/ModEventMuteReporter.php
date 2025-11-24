<?php

namespace SocialDept\Schema\Generated\Tools\Ozone\Moderation\Defs;

use SocialDept\Schema\Data\Data;

/**
 * Mute incoming reports from an account
 *
 * Lexicon: tools.ozone.moderation.defs.modEventMuteReporter
 * Type: object
 *
 * @property string|null $comment
 * @property int|null $durationInHours Indicates how long the account should remain muted. Falsy value here means a permanent mute.
 */
class ModEventMuteReporter extends Data
{

    /**
     * @param  int|null  $durationInHours  Indicates how long the account should remain muted. Falsy value here means a permanent mute.
     */
    public function __construct(
        public readonly ?string $comment = null,
        public readonly ?int $durationInHours = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.modEventMuteReporter';
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
            comment: $data['comment'] ?? null,
            durationInHours: $data['durationInHours'] ?? null
        );
    }

}
