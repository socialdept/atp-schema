<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Actor\Defs;

use SocialDept\AtpSchema\Attributes\Generated;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.actor.defs.savedFeedsPrefV2
 * Type: object
 *
 * @property array<SavedFeed> $items
 *
 * Constraints:
 * - Required: items
 */
#[Generated(regenerate: true)]
class SavedFeedsPrefV2 extends Data
{
    public function __construct(
        public readonly array $items
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.actor.defs.savedFeedsPrefV2';
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
            items: isset($data['items']) ? array_map(fn ($item) => SavedFeed::fromArray($item), $data['items']) : []
        );
    }

}
