<?php

namespace SocialDept\AtpSchema\Generated\Com\Atproto\Sync\SubscribeRepos;

use Carbon\Carbon;
use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Represents a change to an account's status on a host (eg, PDS or Relay). The
 * semantics of this event are that the status is at the host which emitted the
 * event, not necessarily that at the currently active PDS. Eg, a Relay takedown
 * would emit a takedown with active=false, even if the PDS is still active.
 *
 * Lexicon: com.atproto.sync.subscribeRepos.account
 * Type: object
 *
 * @property int $seq
 * @property string $did
 * @property Carbon $time
 * @property bool $active Indicates that the account has a repository which can be fetched from the host that emitted this event.
 * @property string|null $status If active=false, this optional field indicates a reason for why the account is not active.
 *
 * Constraints:
 * - Required: seq, did, time, active
 * - did: Format: did
 * - time: Format: datetime
 */
#[Generated(regenerate: true)]
class Account extends Data
{
    /**
     * @param  bool  $active  Indicates that the account has a repository which can be fetched from the host that emitted this event.
     * @param  string|null  $status  If active=false, this optional field indicates a reason for why the account is not active.
     */
    public function __construct(
        public readonly int $seq,
        public readonly string $did,
        public readonly Carbon $time,
        public readonly bool $active,
        public readonly ?string $status = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.sync.subscribeRepos.account';
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
            seq: $data['seq'],
            did: $data['did'],
            time: Carbon::parse($data['time']),
            active: $data['active'],
            status: $data['status'] ?? null
        );
    }

}
