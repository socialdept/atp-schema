<?php

namespace SocialDept\Schema\Generated\Com\Atproto\Moderation\CreateReport;

use SocialDept\Schema\Data\Data;

/**
 * Moderation tool information for tracing the source of the action
 *
 * Lexicon: com.atproto.moderation.createReport.modTool
 * Type: object
 *
 * @property string $name Name/identifier of the source (e.g., 'bsky-app/android', 'bsky-web/chrome')
 * @property mixed $meta Additional arbitrary metadata about the source
 *
 * Constraints:
 * - Required: name
 */
class ModTool extends Data
{

    /**
     * @param  string  $name  Name/identifier of the source (e.g., 'bsky-app/android', 'bsky-web/chrome')
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
        return 'com.atproto.moderation.createReport.modTool';
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
