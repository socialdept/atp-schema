<?php

namespace SocialDept\AtpSchema\Generated\Com\Atproto\Repo\ApplyWrites;

use SocialDept\AtpSchema\Attributes\Generated;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Operation which creates a new record.
 *
 * Lexicon: com.atproto.repo.applyWrites.create
 * Type: object
 *
 * @property string $collection
 * @property string|null $rkey NOTE: maxLength is redundant with record-key format. Keeping it temporarily to ensure backwards compatibility.
 * @property mixed $value
 *
 * Constraints:
 * - Required: collection, value
 * - collection: Format: nsid
 * - rkey: Max length: 512
 * - rkey: Format: record-key
 */
#[Generated(regenerate: true)]
class Create extends Data
{
    /**
     * @param  string|null  $rkey  NOTE: maxLength is redundant with record-key format. Keeping it temporarily to ensure backwards compatibility.
     */
    public function __construct(
        public readonly string $collection,
        public readonly mixed $value,
        public readonly ?string $rkey = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.repo.applyWrites.create';
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
            value: $data['value'],
            rkey: $data['rkey'] ?? null
        );
    }

}
