<?php

namespace SocialDept\Schema\Generated\Chat\Bsky\Convo\Defs;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\App\Bsky\Richtext\Facet;
use SocialDept\Schema\Support\UnionHelper;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: chat.bsky.convo.defs.messageView
 * Type: object
 *
 * @property string $id
 * @property string $rev
 * @property string $text
 * @property array<Facet>|null $facets Annotations of text (mentions, URLs, hashtags, etc)
 * @property mixed $embed
 * @property array|null $reactions Reactions to this message, in ascending order of creation time.
 * @property mixed $sender
 * @property Carbon $sentAt
 *
 * Constraints:
 * - Required: id, rev, text, sender, sentAt
 * - text: Max length: 10000
 * - text: Max graphemes: 1000
 * - sentAt: Format: datetime
 */
class MessageView extends Data
{
    /**
     * @param  array<Facet>|null  $facets  Annotations of text (mentions, URLs, hashtags, etc)
     * @param  array|null  $reactions  Reactions to this message, in ascending order of creation time.
     */
    public function __construct(
        public readonly string $id,
        public readonly string $rev,
        public readonly string $text,
        public readonly mixed $sender,
        public readonly Carbon $sentAt,
        public readonly ?array $facets = null,
        public readonly mixed $embed = null,
        public readonly ?array $reactions = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'chat.bsky.convo.defs.messageView';
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
            text: $data['text'],
            sender: $data['sender'],
            sentAt: Carbon::parse($data['sentAt']),
            facets: isset($data['facets']) ? array_map(fn ($item) => Facet::fromArray($item), $data['facets']) : [],
            embed: isset($data['embed']) ? UnionHelper::validateOpenUnion($data['embed']) : null,
            reactions: $data['reactions'] ?? []
        );
    }

}
