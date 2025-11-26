<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Embed\Images;

use SocialDept\AtpSchema\Data\BlobReference;
use SocialDept\AtpSchema\Data\Data;
use SocialDept\AtpSchema\Generated\App\Bsky\Embed\Defs\AspectRatio;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.embed.images.image
 * Type: object
 *
 * @property BlobReference $image
 * @property string $alt Alt text description of the image, for accessibility.
 * @property AspectRatio|null $aspectRatio
 *
 * Constraints:
 * - Required: image, alt
 */
class Image extends Data
{
    /**
     * @param  string  $alt  Alt text description of the image, for accessibility.
     */
    public function __construct(
        public readonly BlobReference $image,
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
        return 'app.bsky.embed.images.image';
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
            image: $data['image'],
            alt: $data['alt'],
            aspectRatio: isset($data['aspectRatio']) ? AspectRatio::fromArray($data['aspectRatio']) : null
        );
    }

}
