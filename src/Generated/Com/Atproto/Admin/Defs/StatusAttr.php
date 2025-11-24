<?php

namespace SocialDept\Schema\Generated\Com\Atproto\Admin\Defs;

use SocialDept\Schema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: com.atproto.admin.defs.statusAttr
 * Type: object
 *
 * @property bool $applied
 * @property string|null $ref
 *
 * Constraints:
 * - Required: applied
 */
class StatusAttr extends Data
{
    public function __construct(
        public readonly bool $applied,
        public readonly ?string $ref = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'com.atproto.admin.defs.statusAttr';
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
            applied: $data['applied'],
            ref: $data['ref'] ?? null
        );
    }

}
