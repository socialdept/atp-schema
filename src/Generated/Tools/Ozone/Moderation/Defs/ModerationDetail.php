<?php

namespace SocialDept\Schema\Generated\Tools\Ozone\Moderation\Defs;

use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\Tools\Ozone\Moderation\Defs\ModerationDetail\SubjectStatusView;

/**
 * Lexicon: tools.ozone.moderation.defs.moderationDetail
 * Type: object
 *
 * @property mixed $subjectStatus
 */
class ModerationDetail extends Data
{

    /**
     */
    public function __construct(
        public readonly mixed $subjectStatus = null
    ) {}

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.moderationDetail';
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
            subjectStatus: $data['subjectStatus'] ?? null
        );
    }

}
