<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Embed\Video;

use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Generated\App\Bsky\Embed\Defs\AspectRatio;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.embed.video.view
 * Type: object
 *
 * @property string $cid
 * @property string $playlist
 * @property string|null $thumbnail
 * @property string|null $alt
 * @property AspectRatio|null $aspectRatio
 *
 * Constraints:
 * - Required: cid, playlist
 * - cid: Format: cid
 * - playlist: Format: uri
 * - thumbnail: Format: uri
 * - alt: Max length: 10000
 * - alt: Max graphemes: 1000
 */
class View extends Data
{
    public function __construct(
        public readonly string $cid,
        public readonly string $playlist,
        public readonly ?string $thumbnail = null,
        public readonly ?string $alt = null,
        public readonly ?AspectRatio $aspectRatio = null
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.embed.video.view';
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
            cid: $data['cid'],
            playlist: $data['playlist'],
            thumbnail: $data['thumbnail'] ?? null,
            alt: $data['alt'] ?? null,
            aspectRatio: isset($data['aspectRatio']) ? AspectRatio::fromArray($data['aspectRatio']) : null
        );
    }

}
