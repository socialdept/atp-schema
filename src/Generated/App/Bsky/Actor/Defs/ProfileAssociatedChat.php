<?php

namespace SocialDept\Schema\Generated\App\Bsky\Actor\Defs;

use SocialDept\Schema\Data\Data;

/**
 * Lexicon: app.bsky.actor.defs.profileAssociatedChat
 * Type: object
 *
 * @property string $allowIncoming
 *
 * Constraints:
 * - Required: allowIncoming
 */
class ProfileAssociatedChat extends Data
{

    public function __construct(
        public readonly string $allowIncoming
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.actor.defs.profileAssociatedChat';
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
            allowIncoming: $data['allowIncoming']
        );
    }

}
