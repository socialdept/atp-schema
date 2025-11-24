<?php

namespace SocialDept\Schema\Generated\Com\Atproto\Sync\SubscribeRepos;

use SocialDept\Schema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * A repo operation, ie a mutation of a single record.
 *
 * Lexicon: com.atproto.sync.subscribeRepos.repoOp
 * Type: object
 *
 * @property string $action
 * @property string $path
 * @property string $cid For creates and updates, the new record CID. For deletions, null.
 * @property string|null $prev For updates and deletes, the previous record CID (required for inductive firehose). For creations, field should not be defined.
 *
 * Constraints:
 * - Required: action, path, cid
 */
class RepoOp extends Data
{
    /**
     * @param  string  $cid  For creates and updates, the new record CID. For deletions, null.
     * @param  string|null  $prev  For updates and deletes, the previous record CID (required for inductive firehose). For creations, field should not be defined.
     */
    public function __construct(
        public readonly string $action,
        public readonly string $path,
        public readonly string $cid,
        public readonly ?string $prev = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.sync.subscribeRepos.repoOp';
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
            action: $data['action'],
            path: $data['path'],
            cid: $data['cid'],
            prev: $data['prev'] ?? null
        );
    }

}
