<?php

namespace SocialDept\AtpSchema\Generated\Com\Atproto\Sync\ListRepos;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: com.atproto.sync.listRepos.repo
 * Type: object
 *
 * @property string $did
 * @property string $head Current repo commit CID
 * @property string $rev
 * @property bool|null $active
 * @property string|null $status If active=false, this optional field indicates a possible reason for why the account is not active. If active=false and no status is supplied, then the host makes no claim for why the repository is no longer being hosted.
 *
 * Constraints:
 * - Required: did, head, rev
 * - did: Format: did
 * - head: Format: cid
 * - rev: Format: tid
 */
class Repo extends Data
{
    /**
     * @param  string  $head  Current repo commit CID
     * @param  string|null  $status  If active=false, this optional field indicates a possible reason for why the account is not active. If active=false and no status is supplied, then the host makes no claim for why the repository is no longer being hosted.
     */
    public function __construct(
        public readonly string $did,
        public readonly string $head,
        public readonly string $rev,
        public readonly ?bool $active = null,
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
        return 'com.atproto.sync.listRepos.repo';
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
            head: $data['head'],
            rev: $data['rev'],
            active: $data['active'] ?? null,
            status: $data['status'] ?? null
        );
    }

}
