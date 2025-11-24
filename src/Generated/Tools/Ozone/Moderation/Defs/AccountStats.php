<?php

namespace SocialDept\Schema\Generated\Tools\Ozone\Moderation\Defs;

use SocialDept\Schema\Data\Data;

/**
 * Statistics about a particular account subject
 *
 * Lexicon: tools.ozone.moderation.defs.accountStats
 * Type: object
 *
 * @property int|null $reportCount Total number of reports on the account
 * @property int|null $appealCount Total number of appeals against a moderation action on the account
 * @property int|null $suspendCount Number of times the account was suspended
 * @property int|null $escalateCount Number of times the account was escalated
 * @property int|null $takedownCount Number of times the account was taken down
 */
class AccountStats extends Data
{
    /**
     * @param  int|null  $reportCount  Total number of reports on the account
     * @param  int|null  $appealCount  Total number of appeals against a moderation action on the account
     * @param  int|null  $suspendCount  Number of times the account was suspended
     * @param  int|null  $escalateCount  Number of times the account was escalated
     * @param  int|null  $takedownCount  Number of times the account was taken down
     */
    public function __construct(
        public readonly ?int $reportCount = null,
        public readonly ?int $appealCount = null,
        public readonly ?int $suspendCount = null,
        public readonly ?int $escalateCount = null,
        public readonly ?int $takedownCount = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.accountStats';
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
            reportCount: $data['reportCount'] ?? null,
            appealCount: $data['appealCount'] ?? null,
            suspendCount: $data['suspendCount'] ?? null,
            escalateCount: $data['escalateCount'] ?? null,
            takedownCount: $data['takedownCount'] ?? null
        );
    }

}
