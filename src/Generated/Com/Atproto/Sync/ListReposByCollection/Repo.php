<?php

namespace SocialDept\Schema\Generated\Com\Atproto\Sync\ListReposByCollection;

use SocialDept\Schema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: com.atproto.sync.listReposByCollection.repo
 * Type: object
 *
 * @property string $did
 *
 * Constraints:
 * - Required: did
 * - did: Format: did
 */
class Repo extends Data
{
    public function __construct(
        public readonly string $did
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.sync.listReposByCollection.repo';
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
            did: $data['did']
        );
    }

}
