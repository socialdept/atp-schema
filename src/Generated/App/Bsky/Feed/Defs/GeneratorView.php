<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Feed\Defs;

use SocialDept\AtpSchema\Attributes\Generated;

use Carbon\Carbon;
use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Generated\App\Bsky\Actor\Defs\ProfileView;
use SocialDept\AtpSchema\Generated\App\Bsky\Richtext\Facet;
use SocialDept\AtpSchema\Generated\Com\Atproto\Label\Defs\Label;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.feed.defs.generatorView
 * Type: object
 *
 * @property string $uri
 * @property string $cid
 * @property string $did
 * @property ProfileView $creator
 * @property string $displayName
 * @property string|null $description
 * @property array<Facet>|null $descriptionFacets
 * @property string|null $avatar
 * @property int|null $likeCount
 * @property bool|null $acceptsInteractions
 * @property array<Label>|null $labels
 * @property mixed $viewer
 * @property string|null $contentMode
 * @property Carbon $indexedAt
 *
 * Constraints:
 * - Required: uri, cid, did, creator, displayName, indexedAt
 * - uri: Format: at-uri
 * - cid: Format: cid
 * - did: Format: did
 * - description: Max length: 3000
 * - description: Max graphemes: 300
 * - avatar: Format: uri
 * - likeCount: Minimum: 0
 * - indexedAt: Format: datetime
 */
#[Generated(regenerate: true)]
class GeneratorView extends Data
{
    public function __construct(
        public readonly string $uri,
        public readonly string $cid,
        public readonly string $did,
        public readonly ProfileView $creator,
        public readonly string $displayName,
        public readonly Carbon $indexedAt,
        public readonly ?string $description = null,
        public readonly ?array $descriptionFacets = null,
        public readonly ?string $avatar = null,
        public readonly ?int $likeCount = null,
        public readonly ?bool $acceptsInteractions = null,
        public readonly ?array $labels = null,
        public readonly mixed $viewer = null,
        public readonly ?string $contentMode = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.feed.defs.generatorView';
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
            did: $data['did'],
            creator: ProfileView::fromArray($data['creator']),
            displayName: $data['displayName'],
            indexedAt: Carbon::parse($data['indexedAt']),
            description: $data['description'] ?? null,
            descriptionFacets: isset($data['descriptionFacets']) ? array_map(fn ($item) => Facet::fromArray($item), $data['descriptionFacets']) : [],
            avatar: $data['avatar'] ?? null,
            likeCount: $data['likeCount'] ?? null,
            acceptsInteractions: $data['acceptsInteractions'] ?? null,
            labels: isset($data['labels']) ? array_map(fn ($item) => Label::fromArray($item), $data['labels']) : [],
            viewer: isset($data['viewer']) ? GeneratorViewerState::fromArray($data['viewer']) : null,
            contentMode: $data['contentMode'] ?? null
        );
    }

}
