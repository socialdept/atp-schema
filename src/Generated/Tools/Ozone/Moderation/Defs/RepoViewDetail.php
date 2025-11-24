<?php

namespace SocialDept\Schema\Generated\Tools\Ozone\Moderation\Defs;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\Com\Atproto\Admin\ThreatSignature;
use SocialDept\Schema\Generated\Com\Atproto\Label\Label;
use SocialDept\Schema\Generated\Com\Atproto\Server\InviteCode;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: tools.ozone.moderation.defs.repoViewDetail
 * Type: object
 *
 * @property string $did
 * @property string $handle
 * @property string|null $email
 * @property array $relatedRecords
 * @property Carbon $indexedAt
 * @property mixed $moderation
 * @property array<Label>|null $labels
 * @property InviteCode|null $invitedBy
 * @property array<InviteCode>|null $invites
 * @property bool|null $invitesDisabled
 * @property string|null $inviteNote
 * @property Carbon|null $emailConfirmedAt
 * @property Carbon|null $deactivatedAt
 * @property array<ThreatSignature>|null $threatSignatures
 *
 * Constraints:
 * - Required: did, handle, relatedRecords, indexedAt, moderation
 * - did: Format: did
 * - handle: Format: handle
 * - indexedAt: Format: datetime
 * - emailConfirmedAt: Format: datetime
 * - deactivatedAt: Format: datetime
 */
class RepoViewDetail extends Data
{
    public function __construct(
        public readonly string $did,
        public readonly string $handle,
        public readonly array $relatedRecords,
        public readonly Carbon $indexedAt,
        public readonly mixed $moderation,
        public readonly ?string $email = null,
        public readonly ?array $labels = null,
        public readonly ?InviteCode $invitedBy = null,
        public readonly ?array $invites = null,
        public readonly ?bool $invitesDisabled = null,
        public readonly ?string $inviteNote = null,
        public readonly ?Carbon $emailConfirmedAt = null,
        public readonly ?Carbon $deactivatedAt = null,
        public readonly ?array $threatSignatures = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.repoViewDetail';
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
            handle: $data['handle'],
            relatedRecords: $data['relatedRecords'],
            indexedAt: Carbon::parse($data['indexedAt']),
            moderation: $data['moderation'],
            email: $data['email'] ?? null,
            labels: isset($data['labels']) ? array_map(fn ($item) => Defs::fromArray($item), $data['labels']) : [],
            invitedBy: isset($data['invitedBy']) ? Defs::fromArray($data['invitedBy']) : null,
            invites: isset($data['invites']) ? array_map(fn ($item) => Defs::fromArray($item), $data['invites']) : [],
            invitesDisabled: $data['invitesDisabled'] ?? null,
            inviteNote: $data['inviteNote'] ?? null,
            emailConfirmedAt: isset($data['emailConfirmedAt']) ? Carbon::parse($data['emailConfirmedAt']) : null,
            deactivatedAt: isset($data['deactivatedAt']) ? Carbon::parse($data['deactivatedAt']) : null,
            threatSignatures: isset($data['threatSignatures']) ? array_map(fn ($item) => Defs::fromArray($item), $data['threatSignatures']) : []
        );
    }

}
