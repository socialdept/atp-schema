<?php

namespace SocialDept\Schema\Generated\Chat\Bsky\Convo\Defs;

use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\App\Bsky\Richtext\Facet;
use SocialDept\Schema\Support\UnionHelper;

/**
 * Lexicon: chat.bsky.convo.defs.messageInput
 * Type: object
 *
 * @property string $text
 * @property array<Facet>|null $facets Annotations of text (mentions, URLs, hashtags, etc)
 * @property mixed $embed
 *
 * Constraints:
 * - Required: text
 * - text: Max length: 10000
 * - text: Max graphemes: 1000
 */
class MessageInput extends Data
{
    /**
     * @param  array<Facet>|null  $facets  Annotations of text (mentions, URLs, hashtags, etc)
     */
    public function __construct(
        public readonly string $text,
        public readonly ?array $facets = null,
        public readonly mixed $embed = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'chat.bsky.convo.defs.messageInput';
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
            text: $data['text'],
            facets: isset($data['facets']) ? array_map(fn ($item) => Facet::fromArray($item), $data['facets']) : [],
            embed: isset($data['embed']) ? UnionHelper::validateOpenUnion($data['embed']) : null
        );
    }

}
