<?php

namespace SocialDept\Schema\Generated\Chat\Bsky\Convo\Defs;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: chat.bsky.convo.defs.deletedMessageView
 * Type: object
 *
 * @property string $id
 * @property string $rev
 * @property mixed $sender
 * @property Carbon $sentAt
 *
 * Constraints:
 * - Required: id, rev, sender, sentAt
 * - sentAt: Format: datetime
 */
class DeletedMessageView extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $rev,
        public readonly mixed $sender,
        public readonly Carbon $sentAt
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'chat.bsky.convo.defs.deletedMessageView';
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
            id: $data['id'],
            rev: $data['rev'],
            sender: $data['sender'],
            sentAt: Carbon::parse($data['sentAt'])
        );
    }

}
