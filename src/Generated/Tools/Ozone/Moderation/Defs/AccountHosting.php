<?php

namespace SocialDept\Schema\Generated\Tools\Ozone\Moderation\Defs;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: tools.ozone.moderation.defs.accountHosting
 * Type: object
 *
 * @property string $status
 * @property Carbon|null $updatedAt
 * @property Carbon|null $createdAt
 * @property Carbon|null $deletedAt
 * @property Carbon|null $deactivatedAt
 * @property Carbon|null $reactivatedAt
 *
 * Constraints:
 * - Required: status
 * - updatedAt: Format: datetime
 * - createdAt: Format: datetime
 * - deletedAt: Format: datetime
 * - deactivatedAt: Format: datetime
 * - reactivatedAt: Format: datetime
 */
class AccountHosting extends Data
{
    public function __construct(
        public readonly string $status,
        public readonly ?Carbon $updatedAt = null,
        public readonly ?Carbon $createdAt = null,
        public readonly ?Carbon $deletedAt = null,
        public readonly ?Carbon $deactivatedAt = null,
        public readonly ?Carbon $reactivatedAt = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.accountHosting';
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
            status: $data['status'],
            updatedAt: isset($data['updatedAt']) ? Carbon::parse($data['updatedAt']) : null,
            createdAt: isset($data['createdAt']) ? Carbon::parse($data['createdAt']) : null,
            deletedAt: isset($data['deletedAt']) ? Carbon::parse($data['deletedAt']) : null,
            deactivatedAt: isset($data['deactivatedAt']) ? Carbon::parse($data['deactivatedAt']) : null,
            reactivatedAt: isset($data['reactivatedAt']) ? Carbon::parse($data['reactivatedAt']) : null
        );
    }

}
