<?php

namespace SocialDept\Schema\Generated\Tools\Ozone\Moderation\Defs;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Strike information for an account
 *
 * Lexicon: tools.ozone.moderation.defs.accountStrike
 * Type: object
 *
 * @property int|null $activeStrikeCount Current number of active strikes (excluding expired strikes)
 * @property int|null $totalStrikeCount Total number of strikes ever received (including expired strikes)
 * @property Carbon|null $firstStrikeAt Timestamp of the first strike received
 * @property Carbon|null $lastStrikeAt Timestamp of the most recent strike received
 *
 * Constraints:
 * - firstStrikeAt: Format: datetime
 * - lastStrikeAt: Format: datetime
 */
class AccountStrike extends Data
{
    /**
     * @param  int|null  $activeStrikeCount  Current number of active strikes (excluding expired strikes)
     * @param  int|null  $totalStrikeCount  Total number of strikes ever received (including expired strikes)
     * @param  Carbon|null  $firstStrikeAt  Timestamp of the first strike received
     * @param  Carbon|null  $lastStrikeAt  Timestamp of the most recent strike received
     */
    public function __construct(
        public readonly ?int $activeStrikeCount = null,
        public readonly ?int $totalStrikeCount = null,
        public readonly ?Carbon $firstStrikeAt = null,
        public readonly ?Carbon $lastStrikeAt = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.accountStrike';
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
            activeStrikeCount: $data['activeStrikeCount'] ?? null,
            totalStrikeCount: $data['totalStrikeCount'] ?? null,
            firstStrikeAt: isset($data['firstStrikeAt']) ? Carbon::parse($data['firstStrikeAt']) : null,
            lastStrikeAt: isset($data['lastStrikeAt']) ? Carbon::parse($data['lastStrikeAt']) : null
        );
    }

}
