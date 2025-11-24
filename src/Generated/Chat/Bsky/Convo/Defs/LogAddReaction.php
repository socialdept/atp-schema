<?php

namespace SocialDept\Schema\Generated\Chat\Bsky\Convo\Defs;

use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\Chat\Bsky\Convo\Defs\LogAddReaction\ReactionView;
use SocialDept\Schema\Support\UnionHelper;

/**
 * Lexicon: chat.bsky.convo.defs.logAddReaction
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
class LogAddReaction extends Data
{

    /**
     */
    public function __construct(
        public readonly string $rev,
        public readonly string $convoId,
        public readonly mixed $message,
        public readonly mixed $reaction
    ) {}

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'chat.bsky.convo.defs.logAddReaction';
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
            reaction: $data['reaction']
        );
    }

}
