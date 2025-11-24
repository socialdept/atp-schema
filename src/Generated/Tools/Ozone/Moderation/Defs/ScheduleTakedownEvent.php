<?php

namespace SocialDept\Schema\Generated\Tools\Ozone\Moderation\Defs;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;

/**
 * Logs a scheduled takedown action for an account.
 *
 * Lexicon: tools.ozone.moderation.defs.scheduleTakedownEvent
 * Type: object
 *
 * @property string|null $comment
 * @property Carbon|null $executeAt
 * @property Carbon|null $executeAfter
 * @property Carbon|null $executeUntil
 *
 * Constraints:
 * - executeAt: Format: datetime
 * - executeAfter: Format: datetime
 * - executeUntil: Format: datetime
 */
class ScheduleTakedownEvent extends Data
{

    /**
     */
    public function __construct(
        public readonly ?string $comment = null,
        public readonly ?Carbon $executeAt = null,
        public readonly ?Carbon $executeAfter = null,
        public readonly ?Carbon $executeUntil = null
    ) {}

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.scheduleTakedownEvent';
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
            executeAt: isset($data['executeAt']) ? Carbon::parse($data['executeAt']) : null,
            executeAfter: isset($data['executeAfter']) ? Carbon::parse($data['executeAfter']) : null,
            executeUntil: isset($data['executeUntil']) ? Carbon::parse($data['executeUntil']) : null
        );
    }

}
