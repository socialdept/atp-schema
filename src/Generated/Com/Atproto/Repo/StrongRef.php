<?php

namespace SocialDept\Schema\Generated\Com\Atproto\Repo;

use SocialDept\Schema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * A URI with a content-hash fingerprint.
 *
 * Lexicon: com.atproto.repo.strongRef
 * Type: object
 *
 * @property string $uri
 * @property string $cid
 *
 * Constraints:
 * - Required: uri, cid
 * - uri: Format: at-uri
 * - cid: Format: cid
 */
class StrongRef extends Data
{
    public function __construct(
        public readonly string $uri,
        public readonly string $cid
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.repo.strongRef';
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
            cid: $data['cid']
        );
    }

}
