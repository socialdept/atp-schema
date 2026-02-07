<?php

namespace SocialDept\AtpSchema\Generated\Chat\Bsky\Convo\Defs;

use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: chat.bsky.convo.defs.messageAndReactionView
 * Type: object
 *
 * @property mixed $message
 * @property mixed $reaction
 *
 * Constraints:
 * - Required: message, reaction
 */
#[Generated(regenerate: true)]
class MessageAndReactionView extends Data
{
    public function __construct(
        public readonly mixed $message,
        public readonly mixed $reaction
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'chat.bsky.convo.defs.messageAndReactionView';
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
            message: MessageView::fromArray($data['message']),
            reaction: ReactionView::fromArray($data['reaction'])
        );
    }

}
