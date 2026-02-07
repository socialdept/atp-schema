<?php

namespace SocialDept\AtpSchema\Generated\Chat\Bsky\Convo\Defs;

use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Support\UnionHelper;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: chat.bsky.convo.defs.logRemoveReaction
 * Type: object
 *
 * @property string $rev
 * @property string $convoId
 * @property mixed $message
 * @property mixed $reaction
 *
 * Constraints:
 * - Required: rev, convoId, message, reaction
 */
#[Generated(regenerate: true)]
class LogRemoveReaction extends Data
{
    public function __construct(
        public readonly string $rev,
        public readonly string $convoId,
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
        return 'chat.bsky.convo.defs.logRemoveReaction';
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
            rev: $data['rev'],
            convoId: $data['convoId'],
            message: UnionHelper::validateOpenUnion($data['message']),
            reaction: ReactionView::fromArray($data['reaction'])
        );
    }

}
