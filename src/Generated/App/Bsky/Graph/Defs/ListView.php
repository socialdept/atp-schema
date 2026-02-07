<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Graph\Defs;

use Carbon\Carbon;
use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Generated\App\Bsky\Actor\Defs\ProfileView;
use SocialDept\AtpSchema\Generated\App\Bsky\Richtext\Facet;
use SocialDept\AtpSchema\Generated\Com\Atproto\Label\Defs\Label;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.graph.defs.listView
 * Type: object
 *
 * @property string $uri
 * @property string $cid
 * @property ProfileView $creator
 * @property string $name
 * @property mixed $purpose
 * @property string|null $description
 * @property array<Facet>|null $descriptionFacets
 * @property string|null $avatar
 * @property int|null $listItemCount
 * @property array<Label>|null $labels
 * @property mixed $viewer
 * @property Carbon $indexedAt
 *
 * Constraints:
 * - Required: uri, cid, creator, name, purpose, indexedAt
 * - uri: Format: at-uri
 * - cid: Format: cid
 * - name: Max length: 64
 * - name: Min length: 1
 * - description: Max length: 3000
 * - description: Max graphemes: 300
 * - avatar: Format: uri
 * - listItemCount: Minimum: 0
 * - indexedAt: Format: datetime
 */
#[Generated(regenerate: true)]
class ListView extends Data
{
    public function __construct(
        public readonly string $uri,
        public readonly string $cid,
        public readonly ProfileView $creator,
        public readonly string $name,
        public readonly mixed $purpose,
        public readonly Carbon $indexedAt,
        public readonly ?string $description = null,
        public readonly ?array $descriptionFacets = null,
        public readonly ?string $avatar = null,
        public readonly ?int $listItemCount = null,
        public readonly ?array $labels = null,
        public readonly mixed $viewer = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.graph.defs.listView';
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
            name: $data['name'],
            purpose: ListPurpose::fromArray($data['purpose']),
            indexedAt: Carbon::parse($data['indexedAt']),
            description: $data['description'] ?? null,
            descriptionFacets: isset($data['descriptionFacets']) ? array_map(fn ($item) => Facet::fromArray($item), $data['descriptionFacets']) : [],
            avatar: $data['avatar'] ?? null,
            listItemCount: $data['listItemCount'] ?? null,
            labels: isset($data['labels']) ? array_map(fn ($item) => Label::fromArray($item), $data['labels']) : [],
            viewer: isset($data['viewer']) ? ListViewerState::fromArray($data['viewer']) : null
        );
    }

}
