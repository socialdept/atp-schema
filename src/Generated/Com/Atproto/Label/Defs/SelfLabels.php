<?php

namespace SocialDept\Schema\Generated\Com\Atproto\Label\Defs;

use SocialDept\Schema\Data\Data;

/**
 * Metadata tags on an atproto record, published by the author within the
 * record.
 *
 * Lexicon: com.atproto.label.defs.selfLabels
 * Type: object
 *
 * @property array $values
 *
 * Constraints:
 * - Required: values
 * - values: Max length: 10
 */
class SelfLabels extends Data
{

    public function __construct(
        public readonly array $values
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.label.defs.selfLabels';
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
            values: $data['values'] ?? []
        );
    }

}
