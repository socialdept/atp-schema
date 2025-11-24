<?php

namespace SocialDept\Schema\Generated\App\Bsky\Labeler\Defs;

use SocialDept\Schema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.labeler.defs.labelerViewerState
 * Type: object
 *
 * @property string|null $like
 *
 * Constraints:
 * - like: Format: at-uri
 */
class LabelerViewerState extends Data
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
        return 'app.bsky.labeler.defs.labelerViewerState';
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
