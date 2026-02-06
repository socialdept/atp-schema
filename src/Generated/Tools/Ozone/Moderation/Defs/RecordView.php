<?php

namespace SocialDept\AtpSchema\Generated\Tools\Ozone\Moderation\Defs;

use SocialDept\AtpSchema\Attributes\Generated;

use Carbon\Carbon;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: tools.ozone.moderation.defs.recordView
 * Type: object
 *
 * @property string $uri
 * @property string $cid
 * @property mixed $value
 * @property array<string> $blobCids
 * @property Carbon $indexedAt
 * @property mixed $moderation
 * @property mixed $repo
 *
 * Constraints:
 * - Required: uri, cid, value, blobCids, indexedAt, moderation, repo
 * - uri: Format: at-uri
 * - cid: Format: cid
 * - indexedAt: Format: datetime
 */
#[Generated(regenerate: true)]
class RecordView extends Data
{
    public function __construct(
        public readonly string $uri,
        public readonly string $cid,
        public readonly mixed $value,
        public readonly array $blobCids,
        public readonly Carbon $indexedAt,
        public readonly mixed $moderation,
        public readonly mixed $repo
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'tools.ozone.moderation.defs.recordView';
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
            blobCids: $data['blobCids'],
            indexedAt: Carbon::parse($data['indexedAt']),
            moderation: Moderation::fromArray($data['moderation']),
            repo: RepoView::fromArray($data['repo'])
        );
    }

}
