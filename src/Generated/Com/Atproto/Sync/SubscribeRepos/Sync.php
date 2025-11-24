<?php

namespace SocialDept\Schema\Generated\Com\Atproto\Sync\SubscribeRepos;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;

/**
 * Updates the repo to a new state, without necessarily including that state on
 * the firehose. Used to recover from broken commit streams, data loss
 * incidents, or in situations where upstream host does not know recent state of
 * the repository.
 *
 * Lexicon: com.atproto.sync.subscribeRepos.sync
 * Type: object
 *
 * @property int $seq The stream sequence number of this message.
 * @property string $did The account this repo event corresponds to. Must match that in the commit object.
 * @property string $blocks CAR file containing the commit, as a block. The CAR header must include the commit block CID as the first 'root'.
 * @property string $rev The rev of the commit. This value must match that in the commit object.
 * @property Carbon $time Timestamp of when this message was originally broadcast.
 *
 * Constraints:
 * - Required: seq, did, blocks, rev, time
 * - did: Format: did
 * - blocks: Max length: 10000
 * - time: Format: datetime
 */
class Sync extends Data
{

    /**
     * @param  int  $seq  The stream sequence number of this message.
     * @param  string  $did  The account this repo event corresponds to. Must match that in the commit object.
     * @param  string  $blocks  CAR file containing the commit, as a block. The CAR header must include the commit block CID as the first 'root'.
     * @param  string  $rev  The rev of the commit. This value must match that in the commit object.
     * @param  Carbon  $time  Timestamp of when this message was originally broadcast.
     */
    public function __construct(
        public readonly int $seq,
        public readonly string $did,
        public readonly string $blocks,
        public readonly string $rev,
        public readonly Carbon $time
    ) {}

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.sync.subscribeRepos.sync';
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
            blocks: $data['blocks'],
            rev: $data['rev'],
            time: Carbon::parse($data['time'])
        );
    }

}
