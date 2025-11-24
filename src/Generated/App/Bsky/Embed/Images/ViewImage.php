<?php

namespace SocialDept\Schema\Generated\App\Bsky\Embed\Images;

use SocialDept\Schema\Data\Data;
use SocialDept\Schema\Generated\App\Bsky\Embed\AspectRatio;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.embed.images.viewImage
 * Type: object
 *
 * @property string $thumb Fully-qualified URL where a thumbnail of the image can be fetched. For example, CDN location provided by the App View.
 * @property string $fullsize Fully-qualified URL where a large version of the image can be fetched. May or may not be the exact original blob. For example, CDN location provided by the App View.
 * @property string $alt Alt text description of the image, for accessibility.
 * @property AspectRatio|null $aspectRatio
 *
 * Constraints:
 * - Required: thumb, fullsize, alt
 * - thumb: Format: uri
 * - fullsize: Format: uri
 */
class ViewImage extends Data
{
    /**
     * @param  string  $thumb  Fully-qualified URL where a thumbnail of the image can be fetched. For example, CDN location provided by the App View.
     * @param  string  $fullsize  Fully-qualified URL where a large version of the image can be fetched. May or may not be the exact original blob. For example, CDN location provided by the App View.
     * @param  string  $alt  Alt text description of the image, for accessibility.
     */
    public function __construct(
        public readonly string $thumb,
        public readonly string $fullsize,
        public readonly string $alt,
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
        return 'app.bsky.embed.images.viewImage';
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
            thumb: $data['thumb'],
            fullsize: $data['fullsize'],
            alt: $data['alt'],
            aspectRatio: isset($data['aspectRatio']) ? Defs::fromArray($data['aspectRatio']) : null
        );
    }

}
