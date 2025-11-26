<?php

namespace SocialDept\AtpSchema\Generated\Com\Atproto\Repo\ApplyWrites;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Operation which updates an existing record.
 *
 * Lexicon: com.atproto.repo.applyWrites.update
 * Type: object
 *
 * @property string $collection
 * @property string $rkey
 * @property mixed $value
 *
 * Constraints:
 * - Required: collection, rkey, value
 * - collection: Format: nsid
 * - rkey: Format: record-key
 */
class Update extends Data
{
    public function __construct(
        public readonly string $collection,
        public readonly string $rkey,
        public readonly mixed $value
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.repo.applyWrites.update';
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
            rkey: $data['rkey'],
            value: $data['value']
        );
    }

}
