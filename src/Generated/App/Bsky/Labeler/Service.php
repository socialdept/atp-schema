<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Labeler;

use SocialDept\AtpSchema\Attributes\Generated;

use Carbon\Carbon;
use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Generated\App\Bsky\Labeler\Defs\LabelerPolicies;
use SocialDept\AtpSchema\Generated\Com\Atproto\Moderation\Defs\ReasonType;
use SocialDept\AtpSchema\Generated\Com\Atproto\Moderation\Defs\SubjectType;
use SocialDept\AtpSchema\Support\UnionHelper;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.labeler.service
 * Type: record
 */
#[Generated(regenerate: true)]
class Service extends Data
{
    /**
     * @param  array<ReasonType>|null  $reasonTypes  The set of report reason 'codes' which are in-scope for this service to review and action. These usually align to policy categories. If not defined (distinct from empty array), all reason types are allowed.
     * @param  array<SubjectType>|null  $subjectTypes  The set of subject types (account, record, etc) this service accepts reports on.
     * @param  array<string>|null  $subjectCollections  Set of record types (collection NSIDs) which can be reported to this service. If not defined (distinct from empty array), default is any record type.
     */
    public function __construct(
        public readonly LabelerPolicies $policies,
        public readonly Carbon $createdAt,
        public readonly mixed $labels = null,
        public readonly ?array $reasonTypes = null,
        public readonly ?array $subjectTypes = null,
        public readonly ?array $subjectCollections = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.labeler.service';
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
            policies: LabelerPolicies::fromArray($data['policies']),
            createdAt: Carbon::parse($data['createdAt']),
            labels: isset($data['labels']) ? UnionHelper::validateOpenUnion($data['labels']) : null,
            reasonTypes: isset($data['reasonTypes']) ? array_map(fn ($item) => ReasonType::fromArray($item), $data['reasonTypes']) : [],
            subjectTypes: isset($data['subjectTypes']) ? array_map(fn ($item) => SubjectType::fromArray($item), $data['subjectTypes']) : [],
            subjectCollections: $data['subjectCollections'] ?? null
        );
    }

}
