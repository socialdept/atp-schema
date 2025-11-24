<?php

namespace SocialDept\Schema\Generated\Com\Atproto\Admin\Defs;

use SocialDept\Schema\Data\Data;

/**
 * Lexicon: com.atproto.admin.defs.repoBlobRef
 * Type: object
 *
 * @property string $did
 * @property string $cid
 * @property string|null $recordUri
 *
 * Constraints:
 * - Required: did, cid
 * - did: Format: did
 * - cid: Format: cid
 * - recordUri: Format: at-uri
 */
class RepoBlobRef extends Data
{

    public function __construct(
        public readonly string $did,
        public readonly string $cid,
        public readonly ?string $recordUri = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.admin.defs.repoBlobRef';
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
            cid: $data['cid'],
            recordUri: $data['recordUri'] ?? null
        );
    }

}
