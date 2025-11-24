<?php

namespace SocialDept\Schema\Generated\App\Bsky\Labeler\Defs;

use Carbon\Carbon;
use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\App\Bsky\Actor\Defs\ProfileView;
use SocialDept\Schema\Generated\Com\Atproto\Label\Defs\Label;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.labeler.defs.labelerView
 * Type: object
 *
 * @property string $uri
 * @property string $cid
 * @property ProfileView $creator
 * @property int|null $likeCount
 * @property mixed $viewer
 * @property Carbon $indexedAt
 * @property array<Label>|null $labels
 *
 * Constraints:
 * - Required: uri, cid, creator, indexedAt
 * - uri: Format: at-uri
 * - cid: Format: cid
 * - likeCount: Minimum: 0
 * - indexedAt: Format: datetime
 */
class LabelerView extends Data
{
    public function __construct(
        public readonly string $uri,
        public readonly string $cid,
        public readonly ProfileView $creator,
        public readonly Carbon $indexedAt,
        public readonly ?int $likeCount = null,
        public readonly mixed $viewer = null,
        public readonly ?array $labels = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.labeler.defs.labelerView';
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
            creator: ProfileView::fromArray($data['creator']),
            indexedAt: Carbon::parse($data['indexedAt']),
            likeCount: $data['likeCount'] ?? null,
            viewer: $data['viewer'] ?? null,
            labels: isset($data['labels']) ? array_map(fn ($item) => Label::fromArray($item), $data['labels']) : []
        );
    }

}
