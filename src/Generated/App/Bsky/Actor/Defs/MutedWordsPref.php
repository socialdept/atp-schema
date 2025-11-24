<?php

namespace SocialDept\Schema\Generated\App\Bsky\Actor\Defs;

use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\App\Bsky\Actor\MutedWord;

/**
 * Lexicon: app.bsky.actor.defs.mutedWordsPref
 * Type: object
 *
 * @property array<MutedWord> $items A list of words the account owner has muted.
 *
 * Constraints:
 * - Required: items
 */
class MutedWordsPref extends Data
{

    /**
     * @param  array<MutedWord>  $items  A list of words the account owner has muted.
     */
    public function __construct(
        public readonly array $items
    ) {}

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.actor.defs.mutedWordsPref';
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
            items: isset($data['items']) ? array_map(fn ($item) => Defs::fromArray($item), $data['items']) : []
        );
    }

}
