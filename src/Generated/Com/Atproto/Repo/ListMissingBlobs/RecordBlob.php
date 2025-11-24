<?php

namespace SocialDept\Schema\Generated\Com\Atproto\Repo\ListMissingBlobs;

use SocialDept\Schema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: com.atproto.repo.listMissingBlobs.recordBlob
 * Type: object
 *
 * @property string $cid
 * @property string $recordUri
 *
 * Constraints:
 * - Required: cid, recordUri
 * - cid: Format: cid
 * - recordUri: Format: at-uri
 */
class RecordBlob extends Data
{
    public function __construct(
        public readonly string $cid,
        public readonly string $recordUri
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.repo.listMissingBlobs.recordBlob';
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
            recordUri: $data['recordUri']
        );
    }

}
