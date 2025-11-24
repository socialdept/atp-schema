<?php

namespace SocialDept\Schema\Generated\Tools\Ozone\Moderation\Defs;

use SocialDept\Schema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Moderation tool information for tracing the source of the action
 *
 * Lexicon: tools.ozone.moderation.defs.modTool
 * Type: object
 *
 * @property string $name Name/identifier of the source (e.g., 'automod', 'ozone/workspace')
 * @property mixed $meta Additional arbitrary metadata about the source
 *
 * Constraints:
 * - Required: name
 */
class ModTool extends Data
{
    /**
     * @param  string  $name  Name/identifier of the source (e.g., 'automod', 'ozone/workspace')
     * @param  mixed  $meta  Additional arbitrary metadata about the source
     */
    public function __construct(
        public readonly string $name,
        public readonly mixed $meta = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.modTool';
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
            name: $data['name'],
            meta: $data['meta'] ?? null
        );
    }

}
