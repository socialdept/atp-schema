<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Actor\Defs;

use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.actor.defs.savedFeed
 * Type: object
 *
 * @property string $id
 * @property string $type
 * @property string $value
 * @property bool $pinned
 *
 * Constraints:
 * - Required: id, type, value, pinned
 */
#[Generated(regenerate: true)]
class SavedFeed extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $type,
        public readonly string $value,
        public readonly bool $pinned
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.actor.defs.savedFeed';
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
            id: $data['id'],
            type: $data['type'],
            value: $data['value'],
            pinned: $data['pinned']
        );
    }

}
