<?php

namespace SocialDept\Schema\Generated\Tools\Ozone\Moderation\Defs;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Support\UnionHelper;

/**
 * Lexicon: tools.ozone.moderation.defs.blobView
 * Type: object
 *
 * @property string $cid
 * @property string $mimeType
 * @property int $size
 * @property Carbon $createdAt
 * @property mixed $details
 * @property mixed $moderation
 *
 * Constraints:
 * - Required: cid, mimeType, size, createdAt
 * - cid: Format: cid
 * - createdAt: Format: datetime
 */
class BlobView extends Data
{

    public function __construct(
        public readonly string $cid,
        public readonly string $mimeType,
        public readonly int $size,
        public readonly Carbon $createdAt,
        public readonly mixed $details = null,
        public readonly mixed $moderation = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.blobView';
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
            cid: $data['cid'],
            mimeType: $data['mimeType'],
            size: $data['size'],
            createdAt: Carbon::parse($data['createdAt']),
            details: isset($data['details']) ? UnionHelper::validateOpenUnion($data['details']) : null,
            moderation: $data['moderation'] ?? null
        );
    }

}
