<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Feed\Defs;

use Carbon\Carbon;
use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Generated\App\Bsky\Actor\Defs\ProfileViewBasic;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.feed.defs.reasonRepost
 * Type: object
 *
 * @property ProfileViewBasic $by
 * @property string|null $uri
 * @property string|null $cid
 * @property Carbon $indexedAt
 *
 * Constraints:
 * - Required: by, indexedAt
 * - uri: Format: at-uri
 * - cid: Format: cid
 * - indexedAt: Format: datetime
 */
class ReasonRepost extends Data
{
    public function __construct(
        public readonly ProfileViewBasic $by,
        public readonly Carbon $indexedAt,
        public readonly ?string $uri = null,
        public readonly ?string $cid = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.feed.defs.reasonRepost';
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
            by: ProfileViewBasic::fromArray($data['by']),
            indexedAt: Carbon::parse($data['indexedAt']),
            uri: $data['uri'] ?? null,
            cid: $data['cid'] ?? null
        );
    }

}
