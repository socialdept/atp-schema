<?php

namespace SocialDept\Schema\Generated\App\Bsky\Actor\Defs;

use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\App\Bsky\Actor\Defs\LabelersPref\LabelerPrefItem;

/**
 * Lexicon: app.bsky.actor.defs.labelersPref
 * Type: object
 *
 * @property array $labelers
 *
 * Constraints:
 * - Required: labelers
 */
class LabelersPref extends Data
{

    /**
     */
    public function __construct(
        public readonly array $labelers
    ) {}

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.actor.defs.labelersPref';
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
            labelers: $data['labelers'] ?? []
        );
    }

}
