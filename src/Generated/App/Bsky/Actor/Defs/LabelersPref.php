<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Actor\Defs;

use SocialDept\AtpSchema\Attributes\Generated;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.actor.defs.labelersPref
 * Type: object
 *
 * @property array $labelers
 *
 * Constraints:
 * - Required: labelers
 */
#[Generated(regenerate: true)]
class LabelersPref extends Data
{
    public function __construct(
        public readonly array $labelers
    ) {
    }

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
            labelers: isset($data['labelers']) ? array_map(fn ($item) => LabelerPrefItem::fromArray($item), $data['labelers']) : []
        );
    }

}
