<?php

namespace SocialDept\Schema\Generated\Com\Atproto\Repo\ApplyWrites;

use SocialDept\Schema\Data\Data;

/**
 * Lexicon: com.atproto.repo.applyWrites.updateResult
 * Type: object
 *
 * @property string $uri
 * @property string $cid
 * @property string|null $validationStatus
 *
 * Constraints:
 * - Required: uri, cid
 * - uri: Format: at-uri
 * - cid: Format: cid
 */
class UpdateResult extends Data
{
    /**
     */
    public function __construct(
        public readonly string $uri,
        public readonly string $cid,
        public readonly ?string $validationStatus = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.repo.applyWrites.updateResult';
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
            validationStatus: $data['validationStatus'] ?? null
        );
    }

}
