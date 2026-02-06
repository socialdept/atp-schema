<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Feed;

use SocialDept\AtpSchema\Attributes\Generated;

use Carbon\Carbon;
use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Generated\App\Bsky\Feed\Post\Entity;
use SocialDept\AtpSchema\Generated\App\Bsky\Feed\Post\ReplyRef;
use SocialDept\AtpSchema\Generated\App\Bsky\Richtext\Facet;
use SocialDept\AtpSchema\Support\UnionHelper;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.feed.post
 * Type: record
 */
#[Generated(regenerate: true)]
class Post extends Data
{
    /**
     * @param  string  $text  The primary post content. May be an empty string, if there are embeds.
     * @param  Carbon  $createdAt  Client-declared timestamp when this post was originally created.
     * @param  array<Entity>|null  $entities  DEPRECATED: replaced by app.bsky.richtext.facet.
     * @param  array<Facet>|null  $facets  Annotations of text (mentions, URLs, hashtags, etc)
     * @param  array<string>|null  $langs  Indicates human language of post primary text content.
     * @param  mixed  $labels  Self-label values for this post. Effectively content warnings.
     * @param  array<string>|null  $tags  Additional hashtags, in addition to any included in post text and facets.
     */
    public function __construct(
        public readonly string $text,
        public readonly Carbon $createdAt,
        public readonly ?array $entities = null,
        public readonly ?array $facets = null,
        public readonly ?ReplyRef $reply = null,
        public readonly mixed $embed = null,
        public readonly ?array $langs = null,
        public readonly mixed $labels = null,
        public readonly ?array $tags = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.feed.post';
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
            createdAt: Carbon::parse($data['createdAt']),
            entities: isset($data['entities']) ? array_map(fn ($item) => Entity::fromArray($item), $data['entities']) : [],
            facets: isset($data['facets']) ? array_map(fn ($item) => Facet::fromArray($item), $data['facets']) : [],
            reply: isset($data['reply']) ? ReplyRef::fromArray($data['reply']) : null,
            embed: isset($data['embed']) ? UnionHelper::validateOpenUnion($data['embed']) : null,
            langs: $data['langs'] ?? null,
            labels: isset($data['labels']) ? UnionHelper::validateOpenUnion($data['labels']) : null,
            tags: $data['tags'] ?? null
        );
    }

}
