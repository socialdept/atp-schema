<?php

namespace SocialDept\Schema\Generated\App\Bsky\Unspecced\Defs;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;

/**
 * Lexicon: app.bsky.unspecced.defs.skeletonTrend
 * Type: object
 *
 * @property string $topic
 * @property string $displayName
 * @property string $link
 * @property Carbon $startedAt
 * @property int $postCount
 * @property string|null $status
 * @property string|null $category
 * @property array<string> $dids
 *
 * Constraints:
 * - Required: topic, displayName, link, startedAt, postCount, dids
 * - startedAt: Format: datetime
 */
class SkeletonTrend extends Data
{

    public function __construct(
        public readonly string $topic,
        public readonly string $displayName,
        public readonly string $link,
        public readonly Carbon $startedAt,
        public readonly int $postCount,
        public readonly array $dids,
        public readonly ?string $status = null,
        public readonly ?string $category = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.unspecced.defs.skeletonTrend';
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
            topic: $data['topic'],
            displayName: $data['displayName'],
            link: $data['link'],
            startedAt: Carbon::parse($data['startedAt']),
            postCount: $data['postCount'],
            dids: $data['dids'],
            status: $data['status'] ?? null,
            category: $data['category'] ?? null
        );
    }

}
