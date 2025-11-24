<?php

namespace SocialDept\Schema\Generated\Com\Atproto\Repo\ApplyWrites;

use SocialDept\Schema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Operation which deletes an existing record.
 *
 * Lexicon: com.atproto.repo.applyWrites.delete
 * Type: object
 *
 * @property string $collection
 * @property string $rkey
 *
 * Constraints:
 * - Required: collection, rkey
 * - collection: Format: nsid
 * - rkey: Format: record-key
 */
class Delete extends Data
{
    public function __construct(
        public readonly string $collection,
        public readonly string $rkey
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.repo.applyWrites.delete';
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
            collection: $data['collection'],
            rkey: $data['rkey']
        );
    }

}
