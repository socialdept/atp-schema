<?php

namespace SocialDept\AtpSchema\Generated\Tools\Ozone\Moderation\Defs;

use SocialDept\AtpSchema\Attributes\Generated;

use Carbon\Carbon;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: tools.ozone.moderation.defs.recordHosting
 * Type: object
 *
 * @property string $status
 * @property Carbon|null $updatedAt
 * @property Carbon|null $createdAt
 * @property Carbon|null $deletedAt
 *
 * Constraints:
 * - Required: status
 * - updatedAt: Format: datetime
 * - createdAt: Format: datetime
 * - deletedAt: Format: datetime
 */
#[Generated(regenerate: true)]
class RecordHosting extends Data
{
    public function __construct(
        public readonly string $status,
        public readonly ?Carbon $updatedAt = null,
        public readonly ?Carbon $createdAt = null,
        public readonly ?Carbon $deletedAt = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.recordHosting';
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
            deletedAt: isset($data['deletedAt']) ? Carbon::parse($data['deletedAt']) : null
        );
    }

}
