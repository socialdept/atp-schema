<?php

namespace SocialDept\Schema\Generated\Chat\Bsky\Convo\Defs;

use SocialDept\Schema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: chat.bsky.convo.defs.logLeaveConvo
 * Type: object
 *
 * @property string $rev
 * @property string $convoId
 *
 * Constraints:
 * - Required: rev, convoId
 */
class LogLeaveConvo extends Data
{
    public function __construct(
        public readonly string $rev,
        public readonly string $convoId
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'chat.bsky.convo.defs.logLeaveConvo';
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
            convoId: $data['convoId']
        );
    }

}
