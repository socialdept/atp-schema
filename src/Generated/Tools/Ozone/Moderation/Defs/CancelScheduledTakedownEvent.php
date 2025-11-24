<?php

namespace SocialDept\Schema\Generated\Tools\Ozone\Moderation\Defs;

use SocialDept\Schema\Data\Data;

/**
 * Logs cancellation of a scheduled takedown action for an account.
 *
 * Lexicon: tools.ozone.moderation.defs.cancelScheduledTakedownEvent
 * Type: object
 *
 * @property string|null $comment
 */
class CancelScheduledTakedownEvent extends Data
{
    /**
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
        return 'tools.ozone.moderation.defs.cancelScheduledTakedownEvent';
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
