<?php

namespace SocialDept\AtpSchema\Generated\Chat\Bsky\Convo\Defs;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: chat.bsky.convo.defs.reactionViewSender
 * Type: object
 *
 * @property string $did
 *
 * Constraints:
 * - Required: did
 * - did: Format: did
 */
class ReactionViewSender extends Data
{
    public function __construct(
        public readonly string $did
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'chat.bsky.convo.defs.reactionViewSender';
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
            did: $data['did']
        );
    }

}
