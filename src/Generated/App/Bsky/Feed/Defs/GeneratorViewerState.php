<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Feed\Defs;

use SocialDept\AtpSchema\Attributes\Generated;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.feed.defs.generatorViewerState
 * Type: object
 *
 * @property string|null $like
 *
 * Constraints:
 * - like: Format: at-uri
 */
#[Generated(regenerate: true)]
class GeneratorViewerState extends Data
{
    public function __construct(
        public readonly ?string $like = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.feed.defs.generatorViewerState';
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
            like: $data['like'] ?? null
        );
    }

}
