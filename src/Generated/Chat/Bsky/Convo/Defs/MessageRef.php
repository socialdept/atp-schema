<?php

namespace SocialDept\AtpSchema\Generated\Chat\Bsky\Convo\Defs;

use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: chat.bsky.convo.defs.messageRef
 * Type: object
 *
 * @property string $did
 * @property string $convoId
 * @property string $messageId
 *
 * Constraints:
 * - Required: did, messageId, convoId
 * - did: Format: did
 */
#[Generated(regenerate: true)]
class MessageRef extends Data
{
    public function __construct(
        public readonly string $did,
        public readonly string $convoId,
        public readonly string $messageId
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'chat.bsky.convo.defs.messageRef';
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
            convoId: $data['convoId'],
            messageId: $data['messageId']
        );
    }

}
