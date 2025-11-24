<?php

namespace SocialDept\Schema\Generated\Com\Atproto\Sync\SubscribeRepos;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;

/**
 * Represents a change to an account's identity. Could be an updated handle,
 * signing key, or pds hosting endpoint. Serves as a prod to all downstream
 * services to refresh their identity cache.
 *
 * Lexicon: com.atproto.sync.subscribeRepos.identity
 * Type: object
 *
 * @property int $seq
 * @property string $did
 * @property Carbon $time
 * @property string|null $handle The current handle for the account, or 'handle.invalid' if validation fails. This field is optional, might have been validated or passed-through from an upstream source. Semantics and behaviors for PDS vs Relay may evolve in the future; see atproto specs for more details.
 *
 * Constraints:
 * - Required: seq, did, time
 * - did: Format: did
 * - time: Format: datetime
 * - handle: Format: handle
 */
class Identity extends Data
{
    /**
     * @param  string|null  $handle  The current handle for the account, or 'handle.invalid' if validation fails. This field is optional, might have been validated or passed-through from an upstream source. Semantics and behaviors for PDS vs Relay may evolve in the future; see atproto specs for more details.
     */
    public function __construct(
        public readonly int $seq,
        public readonly string $did,
        public readonly Carbon $time,
        public readonly ?string $handle = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.sync.subscribeRepos.identity';
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
            handle: $data['handle'] ?? null
        );
    }

}
