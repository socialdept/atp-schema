<?php

namespace SocialDept\Schema\Generated\App\Bsky\Actor\Defs;

use SocialDept\Schema\Data\Data;

/**
 * Lexicon: app.bsky.actor.defs.savedFeedsPref
 * Type: object
 *
 * @property array<string> $pinned
 * @property array<string> $saved
 * @property int|null $timelineIndex
 *
 * Constraints:
 * - Required: pinned, saved
 */
class SavedFeedsPref extends Data
{

    public function __construct(
        public readonly array $pinned,
        public readonly array $saved,
        public readonly ?int $timelineIndex = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.actor.defs.savedFeedsPref';
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
            pinned: $data['pinned'],
            saved: $data['saved'],
            timelineIndex: $data['timelineIndex'] ?? null
        );
    }

}
