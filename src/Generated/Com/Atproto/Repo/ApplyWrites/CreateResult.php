<?php

namespace SocialDept\AtpSchema\Generated\Com\Atproto\Repo\ApplyWrites;

use SocialDept\AtpSchema\Attributes\Generated;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: com.atproto.repo.applyWrites.createResult
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
#[Generated(regenerate: true)]
class CreateResult extends Data
{
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
        return 'com.atproto.repo.applyWrites.createResult';
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
