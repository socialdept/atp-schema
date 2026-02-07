<?php

namespace SocialDept\AtpSchema\Generated\Tools\Ozone\Moderation\Defs;

use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: tools.ozone.moderation.defs.reporterStats
 * Type: object
 *
 * @property string $did
 * @property int $accountReportCount The total number of reports made by the user on accounts.
 * @property int $recordReportCount The total number of reports made by the user on records.
 * @property int $reportedAccountCount The total number of accounts reported by the user.
 * @property int $reportedRecordCount The total number of records reported by the user.
 * @property int $takendownAccountCount The total number of accounts taken down as a result of the user's reports.
 * @property int $takendownRecordCount The total number of records taken down as a result of the user's reports.
 * @property int $labeledAccountCount The total number of accounts labeled as a result of the user's reports.
 * @property int $labeledRecordCount The total number of records labeled as a result of the user's reports.
 *
 * Constraints:
 * - Required: did, accountReportCount, recordReportCount, reportedAccountCount, reportedRecordCount, takendownAccountCount, takendownRecordCount, labeledAccountCount, labeledRecordCount
 * - did: Format: did
 */
#[Generated(regenerate: true)]
class ReporterStats extends Data
{
    /**
     * @param  int  $accountReportCount  The total number of reports made by the user on accounts.
     * @param  int  $recordReportCount  The total number of reports made by the user on records.
     * @param  int  $reportedAccountCount  The total number of accounts reported by the user.
     * @param  int  $reportedRecordCount  The total number of records reported by the user.
     * @param  int  $takendownAccountCount  The total number of accounts taken down as a result of the user's reports.
     * @param  int  $takendownRecordCount  The total number of records taken down as a result of the user's reports.
     * @param  int  $labeledAccountCount  The total number of accounts labeled as a result of the user's reports.
     * @param  int  $labeledRecordCount  The total number of records labeled as a result of the user's reports.
     */
    public function __construct(
        public readonly string $did,
        public readonly int $accountReportCount,
        public readonly int $recordReportCount,
        public readonly int $reportedAccountCount,
        public readonly int $reportedRecordCount,
        public readonly int $takendownAccountCount,
        public readonly int $takendownRecordCount,
        public readonly int $labeledAccountCount,
        public readonly int $labeledRecordCount
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.reporterStats';
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
            did: $data['did'],
            accountReportCount: $data['accountReportCount'],
            recordReportCount: $data['recordReportCount'],
            reportedAccountCount: $data['reportedAccountCount'],
            reportedRecordCount: $data['reportedRecordCount'],
            takendownAccountCount: $data['takendownAccountCount'],
            takendownRecordCount: $data['takendownRecordCount'],
            labeledAccountCount: $data['labeledAccountCount'],
            labeledRecordCount: $data['labeledRecordCount']
        );
    }

}
