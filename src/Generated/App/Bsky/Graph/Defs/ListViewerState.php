<?php

namespace SocialDept\Schema\Generated\App\Bsky\Graph\Defs;

use SocialDept\Schema\Data\Data;

/**
 * Lexicon: app.bsky.graph.defs.listViewerState
 * Type: object
 *
 * @property bool|null $muted
 * @property string|null $blocked
 *
 * Constraints:
 * - blocked: Format: at-uri
 */
class ListViewerState extends Data
{
    /**
     */
    public function __construct(
        public readonly ?bool $muted = null,
        public readonly ?string $blocked = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.graph.defs.listViewerState';
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
            muted: $data['muted'] ?? null,
            blocked: $data['blocked'] ?? null
        );
    }

}
