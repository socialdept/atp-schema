<?php

namespace SocialDept\AtpSchema\Generated\Tools\Ozone\Moderation\Defs;

use Carbon\Carbon;
use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Support\UnionHelper;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: tools.ozone.moderation.defs.modEventViewDetail
 * Type: object
 *
 * @property int $id
 * @property mixed $event
 * @property mixed $subject
 * @property array $subjectBlobs
 * @property string $createdBy
 * @property Carbon $createdAt
 * @property mixed $modTool
 *
 * Constraints:
 * - Required: id, event, subject, subjectBlobs, createdBy, createdAt
 * - createdBy: Format: did
 * - createdAt: Format: datetime
 */
class ModEventViewDetail extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly mixed $event,
        public readonly mixed $subject,
        public readonly array $subjectBlobs,
        public readonly string $createdBy,
        public readonly Carbon $createdAt,
        public readonly mixed $modTool = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.modEventViewDetail';
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
            id: $data['id'],
            event: UnionHelper::validateOpenUnion($data['event']),
            subject: UnionHelper::validateOpenUnion($data['subject']),
            subjectBlobs: $data['subjectBlobs'] ?? [],
            createdBy: $data['createdBy'],
            createdAt: Carbon::parse($data['createdAt']),
            modTool: $data['modTool'] ?? null
        );
    }

}
