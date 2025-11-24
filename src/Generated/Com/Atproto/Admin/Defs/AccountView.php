<?php

namespace SocialDept\Schema\Generated\Com\Atproto\Admin\Defs;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\Com\Atproto\Server\InviteCode;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: com.atproto.admin.defs.accountView
 * Type: object
 *
 * @property string $did
 * @property string $handle
 * @property string|null $email
 * @property array|null $relatedRecords
 * @property Carbon $indexedAt
 * @property InviteCode|null $invitedBy
 * @property array<InviteCode>|null $invites
 * @property bool|null $invitesDisabled
 * @property Carbon|null $emailConfirmedAt
 * @property string|null $inviteNote
 * @property Carbon|null $deactivatedAt
 * @property array|null $threatSignatures
 *
 * Constraints:
 * - Required: did, handle, indexedAt
 * - did: Format: did
 * - handle: Format: handle
 * - indexedAt: Format: datetime
 * - emailConfirmedAt: Format: datetime
 * - deactivatedAt: Format: datetime
 */
class AccountView extends Data
{
    public function __construct(
        public readonly string $did,
        public readonly string $handle,
        public readonly Carbon $indexedAt,
        public readonly ?string $email = null,
        public readonly ?array $relatedRecords = null,
        public readonly ?InviteCode $invitedBy = null,
        public readonly ?array $invites = null,
        public readonly ?bool $invitesDisabled = null,
        public readonly ?Carbon $emailConfirmedAt = null,
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
        return 'com.atproto.admin.defs.accountView';
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
            indexedAt: Carbon::parse($data['indexedAt']),
            email: $data['email'] ?? null,
            relatedRecords: $data['relatedRecords'] ?? null,
            invitedBy: isset($data['invitedBy']) ? Defs::fromArray($data['invitedBy']) : null,
            invites: isset($data['invites']) ? array_map(fn ($item) => Defs::fromArray($item), $data['invites']) : [],
            invitesDisabled: $data['invitesDisabled'] ?? null,
            emailConfirmedAt: isset($data['emailConfirmedAt']) ? Carbon::parse($data['emailConfirmedAt']) : null,
            inviteNote: $data['inviteNote'] ?? null,
            deactivatedAt: isset($data['deactivatedAt']) ? Carbon::parse($data['deactivatedAt']) : null,
            threatSignatures: $data['threatSignatures'] ?? []
        );
    }

}
