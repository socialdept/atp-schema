<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Feed\Defs;

use SocialDept\AtpSchema\Attributes\Generated;

use Carbon\Carbon;
use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Generated\App\Bsky\Actor\Defs\ProfileViewBasic;
use SocialDept\AtpSchema\Generated\Com\Atproto\Label\Defs\Label;
use SocialDept\AtpSchema\Support\UnionHelper;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.feed.defs.postView
 * Type: object
 *
 * @property string $uri
 * @property string $cid
 * @property ProfileViewBasic $author
 * @property mixed $record
 * @property mixed $embed
 * @property int|null $bookmarkCount
 * @property int|null $replyCount
 * @property int|null $repostCount
 * @property int|null $likeCount
 * @property int|null $quoteCount
 * @property Carbon $indexedAt
 * @property mixed $viewer
 * @property array<Label>|null $labels
 * @property mixed $threadgate
 * @property mixed $debug Debug information for internal development
 *
 * Constraints:
 * - Required: uri, cid, author, record, indexedAt
 * - uri: Format: at-uri
 * - cid: Format: cid
 * - indexedAt: Format: datetime
 */
#[Generated(regenerate: true)]
class PostView extends Data
{
    /**
     * @param  mixed  $debug  Debug information for internal development
     */
    public function __construct(
        public readonly string $uri,
        public readonly string $cid,
        public readonly ProfileViewBasic $author,
        public readonly mixed $record,
        public readonly Carbon $indexedAt,
        public readonly mixed $embed = null,
        public readonly ?int $bookmarkCount = null,
        public readonly ?int $replyCount = null,
        public readonly ?int $repostCount = null,
        public readonly ?int $likeCount = null,
        public readonly ?int $quoteCount = null,
        public readonly mixed $viewer = null,
        public readonly ?array $labels = null,
        public readonly mixed $threadgate = null,
        public readonly mixed $debug = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.feed.defs.postView';
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
            author: ProfileViewBasic::fromArray($data['author']),
            record: $data['record'],
            indexedAt: Carbon::parse($data['indexedAt']),
            embed: isset($data['embed']) ? UnionHelper::validateOpenUnion($data['embed']) : null,
            bookmarkCount: $data['bookmarkCount'] ?? null,
            replyCount: $data['replyCount'] ?? null,
            repostCount: $data['repostCount'] ?? null,
            likeCount: $data['likeCount'] ?? null,
            quoteCount: $data['quoteCount'] ?? null,
            viewer: isset($data['viewer']) ? ViewerState::fromArray($data['viewer']) : null,
            labels: isset($data['labels']) ? array_map(fn ($item) => Label::fromArray($item), $data['labels']) : [],
            threadgate: isset($data['threadgate']) ? ThreadgateView::fromArray($data['threadgate']) : null,
            debug: $data['debug'] ?? null
        );
    }

}
