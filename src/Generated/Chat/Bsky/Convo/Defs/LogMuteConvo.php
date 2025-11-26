<?php

namespace SocialDept\AtpSchema\Generated\Chat\Bsky\Convo\Defs;

use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: chat.bsky.convo.defs.logMuteConvo
 * Type: object
 *
 * @property string $rev
 * @property string $convoId
 *
 * Constraints:
 * - Required: rev, convoId
 */
class LogMuteConvo extends Data
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
        return 'chat.bsky.convo.defs.logMuteConvo';
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
