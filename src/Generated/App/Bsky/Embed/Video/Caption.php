<?php

namespace SocialDept\AtpSchema\Generated\App\Bsky\Embed\Video;

use SocialDept\AtpSchema\Attributes\Generated;
use SocialDept\AtpSchema\Data\BlobReference;
use SocialDept\AtpSchema\Data\Data;

/**
 * GENERATED CODE - DO NOT EDIT
 *
 * Lexicon: app.bsky.embed.video.caption
 * Type: object
 *
 * @property string $lang
 * @property BlobReference $file
 *
 * Constraints:
 * - Required: lang, file
 * - lang: Format: language
 */
#[Generated(regenerate: true)]
class Caption extends Data
{
    public function __construct(
        public readonly string $lang,
        public readonly BlobReference $file
    ) {
    }

    /**
     * Get the lexicon NSID for this data type.
     *
     * @return string
     */
    public static function getLexicon(): string
    {
        return 'app.bsky.embed.video.caption';
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
            lang: $data['lang'],
            file: BlobReference::fromArray($data['file'])
        );
    }

}
