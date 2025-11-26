<?php

namespace SocialDept\AtpSchema\Generated\Com\Atproto\Admin\Defs;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: com.atproto.admin.defs.repoRef
 * Type: object
 *
 * @property string $did
 *
 * Constraints:
 * - Required: did
 * - did: Format: did
 */
class RepoRef extends Data
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
        return 'com.atproto.admin.defs.repoRef';
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
