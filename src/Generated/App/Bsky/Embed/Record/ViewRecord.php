<?php

namespace SocialDept\Schema\Generated\App\Bsky\Embed\Record;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\App\Bsky\Actor\ProfileViewBasic;
use SocialDept\Schema\Generated\Com\Atproto\Label\Label;

/**
 * Lexicon: app.bsky.embed.record.viewRecord
 * Type: object
 *
 * @property string $uri
 * @property string $cid
 * @property ProfileViewBasic $author
 * @property mixed $value The record data itself.
 * @property array<Label>|null $labels
 * @property int|null $replyCount
 * @property int|null $repostCount
 * @property int|null $likeCount
 * @property int|null $quoteCount
 * @property array|null $embeds
 * @property Carbon $indexedAt
 *
 * Constraints:
 * - Required: uri, cid, author, value, indexedAt
 * - uri: Format: at-uri
 * - cid: Format: cid
 * - indexedAt: Format: datetime
 */
class ViewRecord extends Data
{
    /**
     * @param  mixed  $value  The record data itself.
     */
    public function __construct(
        public readonly string $uri,
        public readonly string $cid,
        public readonly ProfileViewBasic $author,
        public readonly mixed $value,
        public readonly Carbon $indexedAt,
        public readonly ?array $labels = null,
        public readonly ?int $replyCount = null,
        public readonly ?int $repostCount = null,
        public readonly ?int $likeCount = null,
        public readonly ?int $quoteCount = null,
        public readonly ?array $embeds = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.embed.record.viewRecord';
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
            uri: $data['uri'],
            cid: $data['cid'],
            author: Defs::fromArray($data['author']),
            value: $data['value'],
            indexedAt: Carbon::parse($data['indexedAt']),
            labels: isset($data['labels']) ? array_map(fn ($item) => Defs::fromArray($item), $data['labels']) : [],
            replyCount: $data['replyCount'] ?? null,
            repostCount: $data['repostCount'] ?? null,
            likeCount: $data['likeCount'] ?? null,
            quoteCount: $data['quoteCount'] ?? null,
            embeds: $data['embeds'] ?? null
        );
    }

}
