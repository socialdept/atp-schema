<?php

namespace SocialDept\AtpSchema\Generated\Com\Atproto\Label\Defs;

use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Metadata tag on an atproto record, published by the author within the record.
 * Note that schemas should use #selfLabels, not #selfLabel.
 *
 * Lexicon: com.atproto.label.defs.selfLabel
 * Type: object
 *
 * @property string $val The short string name of the value or type of this label.
 *
 * Constraints:
 * - Required: val
 * - val: Max length: 128
 */
#[Generated(regenerate: true)]
class SelfLabel extends Data
{
    /**
     * @param  string  $val  The short string name of the value or type of this label.
     */
    public function __construct(
        public readonly string $val
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.label.defs.selfLabel';
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
            val: $data['val']
        );
    }

}
