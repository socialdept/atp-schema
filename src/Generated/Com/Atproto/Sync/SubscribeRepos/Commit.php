<?php

namespace SocialDept\AtpSchema\Generated\Com\Atproto\Sync\SubscribeRepos;

use Carbon\Carbon;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Represents an update of repository state. Note that empty commits are
 * allowed, which include no repo data changes, but an update to rev and
 * signature.
 *
 * Lexicon: com.atproto.sync.subscribeRepos.commit
 * Type: object
 *
 * @property int $seq The stream sequence number of this message.
 * @property bool $rebase DEPRECATED -- unused
 * @property bool $tooBig DEPRECATED -- replaced by #sync event and data limits. Indicates that this commit contained too many ops, or data size was too large. Consumers will need to make a separate request to get missing data.
 * @property string $repo The repo this event comes from. Note that all other message types name this field 'did'.
 * @property string $commit Repo commit object CID.
 * @property string $rev The rev of the emitted commit. Note that this information is also in the commit object included in blocks, unless this is a tooBig event.
 * @property string $since The rev of the last emitted commit from this repo (if any).
 * @property string $blocks CAR file containing relevant blocks, as a diff since the previous repo state. The commit must be included as a block, and the commit block CID must be the first entry in the CAR header 'roots' list.
 * @property array $ops
 * @property array<string> $blobs
 * @property string|null $prevData The root CID of the MST tree for the previous commit from this repo (indicated by the 'since' revision field in this message). Corresponds to the 'data' field in the repo commit object. NOTE: this field is effectively required for the 'inductive' version of firehose.
 * @property Carbon $time Timestamp of when this message was originally broadcast.
 *
 * Constraints:
 * - Required: seq, rebase, tooBig, repo, commit, rev, since, blocks, ops, blobs, time
 * - repo: Format: did
 * - rev: Format: tid
 * - since: Format: tid
 * - blocks: Max length: 2000000
 * - ops: Max length: 200
 * - time: Format: datetime
 */
class Commit extends Data
{
    /**
     * @param  int  $seq  The stream sequence number of this message.
     * @param  bool  $rebase  DEPRECATED -- unused
     * @param  bool  $tooBig  DEPRECATED -- replaced by #sync event and data limits. Indicates that this commit contained too many ops, or data size was too large. Consumers will need to make a separate request to get missing data.
     * @param  string  $repo  The repo this event comes from. Note that all other message types name this field 'did'.
     * @param  string  $commit  Repo commit object CID.
     * @param  string  $rev  The rev of the emitted commit. Note that this information is also in the commit object included in blocks, unless this is a tooBig event.
     * @param  string  $since  The rev of the last emitted commit from this repo (if any).
     * @param  string  $blocks  CAR file containing relevant blocks, as a diff since the previous repo state. The commit must be included as a block, and the commit block CID must be the first entry in the CAR header 'roots' list.
     * @param  Carbon  $time  Timestamp of when this message was originally broadcast.
     * @param  string|null  $prevData  The root CID of the MST tree for the previous commit from this repo (indicated by the 'since' revision field in this message). Corresponds to the 'data' field in the repo commit object. NOTE: this field is effectively required for the 'inductive' version of firehose.
     */
    public function __construct(
        public readonly int $seq,
        public readonly bool $rebase,
        public readonly bool $tooBig,
        public readonly string $repo,
        public readonly string $commit,
        public readonly string $rev,
        public readonly string $since,
        public readonly string $blocks,
        public readonly array $ops,
        public readonly array $blobs,
        public readonly Carbon $time,
        public readonly ?string $prevData = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.sync.subscribeRepos.commit';
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
            rebase: $data['rebase'],
            tooBig: $data['tooBig'],
            repo: $data['repo'],
            commit: $data['commit'],
            rev: $data['rev'],
            since: $data['since'],
            blocks: $data['blocks'],
            ops: $data['ops'] ?? [],
            blobs: $data['blobs'],
            time: Carbon::parse($data['time']),
            prevData: $data['prevData'] ?? null
        );
    }

}
