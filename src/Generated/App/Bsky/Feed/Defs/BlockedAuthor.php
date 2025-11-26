<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Feed\Defs;

use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Generated\App\Bsky\Actor\Defs\ViewerState;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.feed.defs.blockedAuthor
 * Type: object
 *
 * @property string $did
 * @property ViewerState|null $viewer
 *
 * Constraints:
 * - Required: did
 * - did: Format: did
 */
class BlockedAuthor extends Data
{
    public function __construct(
        public readonly string $did,
        public readonly ?ViewerState $viewer = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.feed.defs.blockedAuthor';
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
            did: $data['did'],
            viewer: isset($data['viewer']) ? ViewerState::fromArray($data['viewer']) : null
        );
    }

}
