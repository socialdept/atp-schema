<?php

namespace SocialDept\Schema\Generated\Com\Atproto\Identity\Defs;

use SocialDept\Schema\Data\Data;

/**
 * Lexicon: com.atproto.identity.defs.identityInfo
 * Type: object
 *
 * @property string $did
 * @property string $handle The validated handle of the account; or 'handle.invalid' if the handle did not bi-directionally match the DID document.
 * @property mixed $didDoc The complete DID document for the identity.
 *
 * Constraints:
 * - Required: did, handle, didDoc
 * - did: Format: did
 * - handle: Format: handle
 */
class IdentityInfo extends Data
{

    /**
     * @param  string  $handle  The validated handle of the account; or 'handle.invalid' if the handle did not bi-directionally match the DID document.
     * @param  mixed  $didDoc  The complete DID document for the identity.
     */
    public function __construct(
        public readonly string $did,
        public readonly string $handle,
        public readonly mixed $didDoc
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.identity.defs.identityInfo';
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
            handle: $data['handle'],
            didDoc: $data['didDoc']
        );
    }

}
