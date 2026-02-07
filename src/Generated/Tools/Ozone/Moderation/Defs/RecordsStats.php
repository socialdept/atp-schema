<?php

namespace SocialDept\AtpSchema\Generated\Tools\Ozone\Moderation\Defs;

use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Statistics about a set of record subject items
 *
 * Lexicon: tools.ozone.moderation.defs.recordsStats
 * Type: object
 *
 * @property int|null $totalReports Cumulative sum of the number of reports on the items in the set
 * @property int|null $reportedCount Number of items that were reported at least once
 * @property int|null $escalatedCount Number of items that were escalated at least once
 * @property int|null $appealedCount Number of items that were appealed at least once
 * @property int|null $subjectCount Total number of item in the set
 * @property int|null $pendingCount Number of item currently in "reviewOpen" or "reviewEscalated" state
 * @property int|null $processedCount Number of item currently in "reviewNone" or "reviewClosed" state
 * @property int|null $takendownCount Number of item currently taken down
 */
#[Generated(regenerate: true)]
class RecordsStats extends Data
{
    /**
     * @param  int|null  $totalReports  Cumulative sum of the number of reports on the items in the set
     * @param  int|null  $reportedCount  Number of items that were reported at least once
     * @param  int|null  $escalatedCount  Number of items that were escalated at least once
     * @param  int|null  $appealedCount  Number of items that were appealed at least once
     * @param  int|null  $subjectCount  Total number of item in the set
     * @param  int|null  $pendingCount  Number of item currently in "reviewOpen" or "reviewEscalated" state
     * @param  int|null  $processedCount  Number of item currently in "reviewNone" or "reviewClosed" state
     * @param  int|null  $takendownCount  Number of item currently taken down
     */
    public function __construct(
        public readonly ?int $totalReports = null,
        public readonly ?int $reportedCount = null,
        public readonly ?int $escalatedCount = null,
        public readonly ?int $appealedCount = null,
        public readonly ?int $subjectCount = null,
        public readonly ?int $pendingCount = null,
        public readonly ?int $processedCount = null,
        public readonly ?int $takendownCount = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.recordsStats';
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
            totalReports: $data['totalReports'] ?? null,
            reportedCount: $data['reportedCount'] ?? null,
            escalatedCount: $data['escalatedCount'] ?? null,
            appealedCount: $data['appealedCount'] ?? null,
            subjectCount: $data['subjectCount'] ?? null,
            pendingCount: $data['pendingCount'] ?? null,
            processedCount: $data['processedCount'] ?? null,
            takendownCount: $data['takendownCount'] ?? null
        );
    }

}
