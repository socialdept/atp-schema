<?php

namespace SocialDept\Schema\Generated\Com\Atproto\Repo\ListRecords;

use SocialDept\Schema\Data\Data;

/**
 * Lexicon: com.atproto.repo.listRecords.record
 * Type: object
 *
 * @property string $uri
 * @property string $cid
 * @property mixed $value
 *
 * Constraints:
 * - Required: uri, cid, value
 * - uri: Format: at-uri
 * - cid: Format: cid
 */
class Record extends Data
{

    public function __construct(
        public readonly string $uri,
        public readonly string $cid,
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
        return 'com.atproto.repo.listRecords.record';
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
            uri: $data['uri'],
            cid: $data['cid'],
            value: $data['value']
        );
    }

}
