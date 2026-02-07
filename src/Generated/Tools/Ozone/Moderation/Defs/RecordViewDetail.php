<?php

namespace SocialDept\AtpSchema\Generated\Tools\Ozone\Moderation\Defs;

use Carbon\Carbon;
use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Generated\Com\Atproto\Label\Defs\Label;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: tools.ozone.moderation.defs.recordViewDetail
 * Type: object
 *
 * @property string $uri
 * @property string $cid
 * @property mixed $value
 * @property array $blobs
 * @property array<Label>|null $labels
 * @property Carbon $indexedAt
 * @property mixed $moderation
 * @property mixed $repo
 *
 * Constraints:
 * - Required: uri, cid, value, blobs, indexedAt, moderation, repo
 * - uri: Format: at-uri
 * - cid: Format: cid
 * - indexedAt: Format: datetime
 */
#[Generated(regenerate: true)]
class RecordViewDetail extends Data
{
    public function __construct(
        public readonly string $uri,
        public readonly string $cid,
        public readonly mixed $value,
        public readonly array $blobs,
        public readonly Carbon $indexedAt,
        public readonly mixed $moderation,
        public readonly mixed $repo,
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
        return 'tools.ozone.moderation.defs.recordViewDetail';
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
            value: $data['value'],
            blobs: isset($data['blobs']) ? array_map(fn ($item) => BlobView::fromArray($item), $data['blobs']) : [],
            indexedAt: Carbon::parse($data['indexedAt']),
            moderation: ModerationDetail::fromArray($data['moderation']),
            repo: RepoView::fromArray($data['repo']),
            labels: isset($data['labels']) ? array_map(fn ($item) => Label::fromArray($item), $data['labels']) : []
        );
    }

}
