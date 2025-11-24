<?php

namespace SocialDept\Schema\Generated\Com\Atproto\Repo\Defs;

use SocialDept\Schema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: com.atproto.repo.defs.commitMeta
 * Type: object
 *
 * @property string $cid
 * @property string $rev
 *
 * Constraints:
 * - Required: cid, rev
 * - cid: Format: cid
 * - rev: Format: tid
 */
class CommitMeta extends Data
{
    public function __construct(
        public readonly string $cid,
        public readonly string $rev
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.repo.defs.commitMeta';
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
            cid: $data['cid'],
            rev: $data['rev']
        );
    }

}
