<?php

namespace SocialDept\Schema\Generated\Tools\Ozone\Moderation\Defs;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Support\UnionHelper;

/**
 * Lexicon: tools.ozone.moderation.defs.modEventView
 * Type: object
 *
 * @property int $id
 * @property mixed $event
 * @property mixed $subject
 * @property array<string> $subjectBlobCids
 * @property string $createdBy
 * @property Carbon $createdAt
 * @property string|null $creatorHandle
 * @property string|null $subjectHandle
 * @property mixed $modTool
 *
 * Constraints:
 * - Required: id, event, subject, subjectBlobCids, createdBy, createdAt
 * - createdBy: Format: did
 * - createdAt: Format: datetime
 */
class ModEventView extends Data
{
    /**
     */
    public function __construct(
        public readonly int $id,
        public readonly mixed $event,
        public readonly mixed $subject,
        public readonly array $subjectBlobCids,
        public readonly string $createdBy,
        public readonly Carbon $createdAt,
        public readonly ?string $creatorHandle = null,
        public readonly ?string $subjectHandle = null,
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
        return 'tools.ozone.moderation.defs.modEventView';
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
            subjectBlobCids: $data['subjectBlobCids'],
            createdBy: $data['createdBy'],
            createdAt: Carbon::parse($data['createdAt']),
            creatorHandle: $data['creatorHandle'] ?? null,
            subjectHandle: $data['subjectHandle'] ?? null,
            modTool: $data['modTool'] ?? null
        );
    }

}
