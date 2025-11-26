<?php

namespace SocialDept\AtpSchema\Generated\Com\Atproto\Admin\Defs;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: com.atproto.admin.defs.threatSignature
 * Type: object
 *
 * @property string $property
 * @property string $value
 *
 * Constraints:
 * - Required: property, value
 */
class ThreatSignature extends Data
{
    public function __construct(
        public readonly string $property,
        public readonly string $value
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.admin.defs.threatSignature';
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
            property: $data['property'],
            value: $data['value']
        );
    }

}
