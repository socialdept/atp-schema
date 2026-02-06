<?php

namespace SocialDept\AtpSchema\Generated\Tools\Ozone\Moderation\Defs;

use SocialDept\AtpSchema\Attributes\Generated;

use Carbon\Carbon;
use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Support\UnionHelper;

/**
 * GENERATED CODE - DO NOT EDIT
 *
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
#[Generated(regenerate: true)]
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
            moderation: isset($data['moderation']) ? Moderation::fromArray($data['moderation']) : null
        );
    }

}
