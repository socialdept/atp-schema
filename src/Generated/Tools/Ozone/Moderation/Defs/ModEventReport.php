<?php

namespace SocialDept\Schema\Generated\Tools\Ozone\Moderation\Defs;

use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\Com\Atproto\Moderation\Defs\ReasonType;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Report a subject
 *
 * Lexicon: tools.ozone.moderation.defs.modEventReport
 * Type: object
 *
 * @property string|null $comment
 * @property bool|null $isReporterMuted Set to true if the reporter was muted from reporting at the time of the event. These reports won't impact the reviewState of the subject.
 * @property ReasonType $reportType
 *
 * Constraints:
 * - Required: reportType
 */
class ModEventReport extends Data
{
    /**
     * @param  bool|null  $isReporterMuted  Set to true if the reporter was muted from reporting at the time of the event. These reports won't impact the reviewState of the subject.
     */
    public function __construct(
        public readonly ReasonType $reportType,
        public readonly ?string $comment = null,
        public readonly ?bool $isReporterMuted = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.modEventReport';
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
            reportType: ReasonType::fromArray($data['reportType']),
            comment: $data['comment'] ?? null,
            isReporterMuted: $data['isReporterMuted'] ?? null
        );
    }

}
