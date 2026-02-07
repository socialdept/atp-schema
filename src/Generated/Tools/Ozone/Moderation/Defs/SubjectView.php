<?php

namespace SocialDept\AtpSchema\Generated\Tools\Ozone\Moderation\Defs;

use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Generated\Com\Atproto\Moderation\Defs\SubjectType;
use SocialDept\AtpSchema\Support\UnionHelper;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Detailed view of a subject. For record subjects, the author's repo and
 * profile will be returned.
 *
 * Lexicon: tools.ozone.moderation.defs.subjectView
 * Type: object
 *
 * @property SubjectType $type
 * @property string $subject
 * @property mixed $status
 * @property mixed $repo
 * @property mixed $profile
 * @property mixed $record
 *
 * Constraints:
 * - Required: type, subject
 */
#[Generated(regenerate: true)]
class SubjectView extends Data
{
    public function __construct(
        public readonly SubjectType $type,
        public readonly string $subject,
        public readonly mixed $status = null,
        public readonly mixed $repo = null,
        public readonly mixed $profile = null,
        public readonly mixed $record = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.subjectView';
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
            type: SubjectType::fromArray($data['type']),
            subject: $data['subject'],
            status: isset($data['status']) ? SubjectStatusView::fromArray($data['status']) : null,
            repo: isset($data['repo']) ? RepoViewDetail::fromArray($data['repo']) : null,
            profile: isset($data['profile']) ? UnionHelper::validateOpenUnion($data['profile']) : null,
            record: isset($data['record']) ? RecordViewDetail::fromArray($data['record']) : null
        );
    }

}
