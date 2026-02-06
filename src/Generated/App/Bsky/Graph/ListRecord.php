<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Graph;

use SocialDept\AtpSchema\Attributes\Generated;

use Carbon\Carbon;
use SocialDept\AtpSchema\Data\BlobReference;
use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Generated\App\Bsky\Graph\Defs\ListPurpose;
use SocialDept\AtpSchema\Generated\App\Bsky\Richtext\Facet;
use SocialDept\AtpSchema\Support\UnionHelper;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.graph.list
 * Type: record
 */
#[Generated(regenerate: true)]
class ListRecord extends Data
{
    /**
     * @param  ListPurpose  $purpose  Defines the purpose of the list (aka, moderation-oriented or curration-oriented)
     * @param  string  $name  Display name for list; can not be empty.
     */
    public function __construct(
        public readonly ListPurpose $purpose,
        public readonly string $name,
        public readonly Carbon $createdAt,
        public readonly ?string $description = null,
        public readonly ?array $descriptionFacets = null,
        public readonly ?BlobReference $avatar = null,
        public readonly mixed $labels = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.graph.list';
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
            purpose: ListPurpose::fromArray($data['purpose']),
            name: $data['name'],
            createdAt: Carbon::parse($data['createdAt']),
            description: $data['description'] ?? null,
            descriptionFacets: isset($data['descriptionFacets']) ? array_map(fn ($item) => Facet::fromArray($item), $data['descriptionFacets']) : [],
            avatar: isset($data['avatar']) ? BlobReference::fromArray($data['avatar']) : null,
            labels: isset($data['labels']) ? UnionHelper::validateOpenUnion($data['labels']) : null
        );
    }

}
