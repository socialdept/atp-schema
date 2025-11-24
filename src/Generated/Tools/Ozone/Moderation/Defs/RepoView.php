<?php

namespace SocialDept\Schema\Generated\Tools\Ozone\Moderation\Defs;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\Com\Atproto\Admin\ThreatSignature;
use SocialDept\Schema\Generated\Com\Atproto\Server\InviteCode;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: tools.ozone.moderation.defs.repoView
 * Type: object
 *
 * @property string $did
 * @property string $handle
 * @property string|null $email
 * @property array $relatedRecords
 * @property Carbon $indexedAt
 * @property mixed $moderation
 * @property InviteCode|null $invitedBy
 * @property bool|null $invitesDisabled
 * @property string|null $inviteNote
 * @property Carbon|null $deactivatedAt
 * @property array<ThreatSignature>|null $threatSignatures
 *
 * Constraints:
 * - Required: did, handle, relatedRecords, indexedAt, moderation
 * - did: Format: did
 * - handle: Format: handle
 * - indexedAt: Format: datetime
 * - deactivatedAt: Format: datetime
 */
class RepoView extends Data
{
    public function __construct(
        public readonly string $did,
        public readonly string $handle,
        public readonly array $relatedRecords,
        public readonly Carbon $indexedAt,
        public readonly mixed $moderation,
        public readonly ?string $email = null,
        public readonly ?InviteCode $invitedBy = null,
        public readonly ?bool $invitesDisabled = null,
        public readonly ?string $inviteNote = null,
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
        return 'tools.ozone.moderation.defs.repoView';
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
            invitedBy: isset($data['invitedBy']) ? Defs::fromArray($data['invitedBy']) : null,
            invitesDisabled: $data['invitesDisabled'] ?? null,
            inviteNote: $data['inviteNote'] ?? null,
            deactivatedAt: isset($data['deactivatedAt']) ? Carbon::parse($data['deactivatedAt']) : null,
            threatSignatures: isset($data['threatSignatures']) ? array_map(fn ($item) => Defs::fromArray($item), $data['threatSignatures']) : []
        );
    }

}
