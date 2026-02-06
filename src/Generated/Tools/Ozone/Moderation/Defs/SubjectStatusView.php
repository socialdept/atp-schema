<?php

namespace SocialDept\AtpSchema\Generated\Tools\Ozone\Moderation\Defs;

use SocialDept\AtpSchema\Attributes\Generated;

use Carbon\Carbon;
use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Support\UnionHelper;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: tools.ozone.moderation.defs.subjectStatusView
 * Type: object
 *
 * @property int $id
 * @property mixed $subject
 * @property mixed $hosting
 * @property array<string>|null $subjectBlobCids
 * @property string|null $subjectRepoHandle
 * @property Carbon $updatedAt Timestamp referencing when the last update was made to the moderation status of the subject
 * @property Carbon $createdAt Timestamp referencing the first moderation status impacting event was emitted on the subject
 * @property mixed $reviewState
 * @property string|null $comment Sticky comment on the subject.
 * @property int|null $priorityScore Numeric value representing the level of priority. Higher score means higher priority.
 * @property Carbon|null $muteUntil
 * @property Carbon|null $muteReportingUntil
 * @property string|null $lastReviewedBy
 * @property Carbon|null $lastReviewedAt
 * @property Carbon|null $lastReportedAt
 * @property Carbon|null $lastAppealedAt Timestamp referencing when the author of the subject appealed a moderation action
 * @property bool|null $takendown
 * @property bool|null $appealed True indicates that the a previously taken moderator action was appealed against, by the author of the content. False indicates last appeal was resolved by moderators.
 * @property Carbon|null $suspendUntil
 * @property array<string>|null $tags
 * @property mixed $accountStats Statistics related to the account subject
 * @property mixed $recordsStats Statistics related to the record subjects authored by the subject's account
 * @property mixed $accountStrike Strike information for the account (account-level only)
 * @property string|null $ageAssuranceState Current age assurance state of the subject.
 * @property string|null $ageAssuranceUpdatedBy Whether or not the last successful update to age assurance was made by the user or admin.
 *
 * Constraints:
 * - Required: id, subject, createdAt, updatedAt, reviewState
 * - updatedAt: Format: datetime
 * - createdAt: Format: datetime
 * - priorityScore: Maximum: 100
 * - priorityScore: Minimum: 0
 * - muteUntil: Format: datetime
 * - muteReportingUntil: Format: datetime
 * - lastReviewedBy: Format: did
 * - lastReviewedAt: Format: datetime
 * - lastReportedAt: Format: datetime
 * - lastAppealedAt: Format: datetime
 * - suspendUntil: Format: datetime
 */
#[Generated(regenerate: true)]
class SubjectStatusView extends Data
{
    /**
     * @param  Carbon  $updatedAt  Timestamp referencing when the last update was made to the moderation status of the subject
     * @param  Carbon  $createdAt  Timestamp referencing the first moderation status impacting event was emitted on the subject
     * @param  string|null  $comment  Sticky comment on the subject.
     * @param  int|null  $priorityScore  Numeric value representing the level of priority. Higher score means higher priority.
     * @param  Carbon|null  $lastAppealedAt  Timestamp referencing when the author of the subject appealed a moderation action
     * @param  bool|null  $appealed  True indicates that the a previously taken moderator action was appealed against, by the author of the content. False indicates last appeal was resolved by moderators.
     * @param  mixed  $accountStats  Statistics related to the account subject
     * @param  mixed  $recordsStats  Statistics related to the record subjects authored by the subject's account
     * @param  mixed  $accountStrike  Strike information for the account (account-level only)
     * @param  string|null  $ageAssuranceState  Current age assurance state of the subject.
     * @param  string|null  $ageAssuranceUpdatedBy  Whether or not the last successful update to age assurance was made by the user or admin.
     */
    public function __construct(
        public readonly int $id,
        public readonly mixed $subject,
        public readonly Carbon $updatedAt,
        public readonly Carbon $createdAt,
        public readonly mixed $reviewState,
        public readonly mixed $hosting = null,
        public readonly ?array $subjectBlobCids = null,
        public readonly ?string $subjectRepoHandle = null,
        public readonly ?string $comment = null,
        public readonly ?int $priorityScore = null,
        public readonly ?Carbon $muteUntil = null,
        public readonly ?Carbon $muteReportingUntil = null,
        public readonly ?string $lastReviewedBy = null,
        public readonly ?Carbon $lastReviewedAt = null,
        public readonly ?Carbon $lastReportedAt = null,
        public readonly ?Carbon $lastAppealedAt = null,
        public readonly ?bool $takendown = null,
        public readonly ?bool $appealed = null,
        public readonly ?Carbon $suspendUntil = null,
        public readonly ?array $tags = null,
        public readonly mixed $accountStats = null,
        public readonly mixed $recordsStats = null,
        public readonly mixed $accountStrike = null,
        public readonly ?string $ageAssuranceState = null,
        public readonly ?string $ageAssuranceUpdatedBy = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.subjectStatusView';
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
            subject: UnionHelper::validateOpenUnion($data['subject']),
            updatedAt: Carbon::parse($data['updatedAt']),
            createdAt: Carbon::parse($data['createdAt']),
            reviewState: SubjectReviewState::fromArray($data['reviewState']),
            hosting: isset($data['hosting']) ? UnionHelper::validateOpenUnion($data['hosting']) : null,
            subjectBlobCids: $data['subjectBlobCids'] ?? null,
            subjectRepoHandle: $data['subjectRepoHandle'] ?? null,
            comment: $data['comment'] ?? null,
            priorityScore: $data['priorityScore'] ?? null,
            muteUntil: isset($data['muteUntil']) ? Carbon::parse($data['muteUntil']) : null,
            muteReportingUntil: isset($data['muteReportingUntil']) ? Carbon::parse($data['muteReportingUntil']) : null,
            lastReviewedBy: $data['lastReviewedBy'] ?? null,
            lastReviewedAt: isset($data['lastReviewedAt']) ? Carbon::parse($data['lastReviewedAt']) : null,
            lastReportedAt: isset($data['lastReportedAt']) ? Carbon::parse($data['lastReportedAt']) : null,
            lastAppealedAt: isset($data['lastAppealedAt']) ? Carbon::parse($data['lastAppealedAt']) : null,
            takendown: $data['takendown'] ?? null,
            appealed: $data['appealed'] ?? null,
            suspendUntil: isset($data['suspendUntil']) ? Carbon::parse($data['suspendUntil']) : null,
            tags: $data['tags'] ?? null,
            accountStats: isset($data['accountStats']) ? AccountStats::fromArray($data['accountStats']) : null,
            recordsStats: isset($data['recordsStats']) ? RecordsStats::fromArray($data['recordsStats']) : null,
            accountStrike: isset($data['accountStrike']) ? AccountStrike::fromArray($data['accountStrike']) : null,
            ageAssuranceState: $data['ageAssuranceState'] ?? null,
            ageAssuranceUpdatedBy: $data['ageAssuranceUpdatedBy'] ?? null
        );
    }

}
