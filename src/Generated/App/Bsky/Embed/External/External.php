<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Embed\External;

use SocialDept\AtpSchema\Data\BlobReference;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.embed.external.external
 * Type: object
 *
 * @property string $uri
 * @property string $title
 * @property string $description
 * @property BlobReference|null $thumb
 *
 * Constraints:
 * - Required: uri, title, description
 * - uri: Format: uri
 */
class External extends Data
{
    public function __construct(
        public readonly string $uri,
        public readonly string $title,
        public readonly string $description,
        public readonly ?BlobReference $thumb = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.embed.external.external';
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
            title: $data['title'],
            description: $data['description'],
            thumb: $data['thumb'] ?? null
        );
    }

}
