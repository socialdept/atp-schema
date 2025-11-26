<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Labeler\Defs;

use Carbon\Carbon;
use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Generated\App\Bsky\Actor\Defs\ProfileView;
use SocialDept\AtpSchema\Generated\Com\Atproto\Label\Defs\Label;
use SocialDept\AtpSchema\Generated\Com\Atproto\Moderation\Defs\ReasonType;
use SocialDept\AtpSchema\Generated\Com\Atproto\Moderation\Defs\SubjectType;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.labeler.defs.labelerViewDetailed
 * Type: object
 *
 * @property string $uri
 * @property string $cid
 * @property ProfileView $creator
 * @property LabelerPolicies $policies
 * @property int|null $likeCount
 * @property mixed $viewer
 * @property Carbon $indexedAt
 * @property array<Label>|null $labels
 * @property array<ReasonType>|null $reasonTypes The set of report reason 'codes' which are in-scope for this service to review and action. These usually align to policy categories. If not defined (distinct from empty array), all reason types are allowed.
 * @property array<SubjectType>|null $subjectTypes The set of subject types (account, record, etc) this service accepts reports on.
 * @property array<string>|null $subjectCollections Set of record types (collection NSIDs) which can be reported to this service. If not defined (distinct from empty array), default is any record type.
 *
 * Constraints:
 * - Required: uri, cid, creator, policies, indexedAt
 * - uri: Format: at-uri
 * - cid: Format: cid
 * - likeCount: Minimum: 0
 * - indexedAt: Format: datetime
 */
class LabelerViewDetailed extends Data
{
    /**
     * @param  array<ReasonType>|null  $reasonTypes  The set of report reason 'codes' which are in-scope for this service to review and action. These usually align to policy categories. If not defined (distinct from empty array), all reason types are allowed.
     * @param  array<SubjectType>|null  $subjectTypes  The set of subject types (account, record, etc) this service accepts reports on.
     * @param  array<string>|null  $subjectCollections  Set of record types (collection NSIDs) which can be reported to this service. If not defined (distinct from empty array), default is any record type.
     */
    public function __construct(
        public readonly string $uri,
        public readonly string $cid,
        public readonly ProfileView $creator,
        public readonly LabelerPolicies $policies,
        public readonly Carbon $indexedAt,
        public readonly ?int $likeCount = null,
        public readonly mixed $viewer = null,
        public readonly ?array $labels = null,
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
        return 'app.bsky.labeler.defs.labelerViewDetailed';
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
            uri: $data['uri'],
            cid: $data['cid'],
            creator: ProfileView::fromArray($data['creator']),
            policies: LabelerPolicies::fromArray($data['policies']),
            indexedAt: Carbon::parse($data['indexedAt']),
            likeCount: $data['likeCount'] ?? null,
            viewer: $data['viewer'] ?? null,
            labels: isset($data['labels']) ? array_map(fn ($item) => Label::fromArray($item), $data['labels']) : [],
            reasonTypes: isset($data['reasonTypes']) ? array_map(fn ($item) => ReasonType::fromArray($item), $data['reasonTypes']) : [],
            subjectTypes: isset($data['subjectTypes']) ? array_map(fn ($item) => SubjectType::fromArray($item), $data['subjectTypes']) : [],
            subjectCollections: $data['subjectCollections'] ?? null
        );
    }

}
