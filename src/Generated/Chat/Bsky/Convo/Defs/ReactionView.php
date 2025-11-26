<?php

namespace SocialDept\AtpSchema\Generated\Chat\Bsky\Convo\Defs;

use Carbon\Carbon;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: chat.bsky.convo.defs.reactionView
 * Type: object
 *
 * @property string $value
 * @property mixed $sender
 * @property Carbon $createdAt
 *
 * Constraints:
 * - Required: value, sender, createdAt
 * - createdAt: Format: datetime
 */
class ReactionView extends Data
{
    public function __construct(
        public readonly string $value,
        public readonly mixed $sender,
        public readonly Carbon $createdAt
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'chat.bsky.convo.defs.reactionView';
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
            value: $data['value'],
            sender: $data['sender'],
            createdAt: Carbon::parse($data['createdAt'])
        );
    }

}
