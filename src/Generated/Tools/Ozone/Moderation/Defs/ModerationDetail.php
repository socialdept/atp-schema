<?php

namespace SocialDept\AtpSchema\Generated\Tools\Ozone\Moderation\Defs;

use SocialDept\AtpSchema\Attributes\Generated;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: tools.ozone.moderation.defs.moderationDetail
 * Type: object
 *
 * @property mixed $subjectStatus
 */
#[Generated(regenerate: true)]
class ModerationDetail extends Data
{
    public function __construct(
        public readonly mixed $subjectStatus = null
    ) {
    }

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
            subjectStatus: isset($data['subjectStatus']) ? SubjectStatusView::fromArray($data['subjectStatus']) : null
        );
    }

}
